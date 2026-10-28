/* Student pages. */
(function () {
  const P = SMS.pages; const ui = SMS.ui;
  const fmtGpa = g => g == null ? '—' : g.toFixed(2);
  const classOf = g => g == null ? '—' : g >= 4.4 ? 'First Class' : g >= 3.6 ? 'Second Upper' : g >= 2.8 ? 'Second Lower' : g >= 2 ? 'Pass' : 'Probation';

  P.studentDashboard = (el, user) => {
    const gpa = SMS.gpa(user.id), fee = SMS.feeSummary(user.id), att = SMS.attendancePct(user.id);
    const courses = SMS.enrolledCourses(user.id);
    const notes = SMS.db.announcements.filter(n => n.audience !== 'Staff').sort((a, b) => b.date.localeCompare(a.date)).slice(0, 3);
    el.innerHTML = ui.heading(`Welcome back, ${ui.esc(user.name.split(' ')[0])}`, `${ui.esc(user.programme)} · Year ${user.year} · ${ui.esc(user.regNo)}`, ui.btn('View results', ui.url('student/results.html'), 'award')) +
      `<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        ${ui.stat('trending-up', 'Current GPA', fmtGpa(gpa), classOf(gpa))}
        ${ui.stat('book-open', 'Credit units', SMS.credits(user.id), courses.length + ' courses this semester')}
        ${ui.stat('calendar-check', 'Attendance', att == null ? '—' : att + '%', att != null && att < 75 ? 'Below the 75% requirement' : 'Meets the 75% requirement', att != null && att < 75 ? 'gold' : '')}
        ${ui.stat('wallet', 'Fee balance', ui.money(fee.balance), fee.balance <= 0 ? 'Fully paid' : 'of ' + ui.money(fee.total), 'gold')}</div>
      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">${ui.card('This semester', ui.table(['Course', 'Lecturer', 'Attendance', 'Grade'], courses.map(c => {
          const a = SMS.attendancePct(user.id, c.id); const l = SMS.lecturer(c.lecturerId);
          return ui.tr([`<p class="font-medium">${ui.esc(c.title)}</p><p class="text-xs text-slate-500">${ui.esc(c.code)} · ${c.credits} CU</p>`, ui.esc(l ? l.name : '—'), a == null ? '—' : `<div class="w-28">${ui.bar(a, ui.attTone(a))}<p class="text-xs text-slate-500 mt-1">${a}%</p></div>`, ui.gradeBadge(SMS.result(user.id, c.id))]);
        }), 'You are not enrolled in any course yet.'), `<a href="${ui.url('student/courses.html')}" class="text-xs text-[#2563eb]">Manage courses</a>`)}</div>
        <div>${ui.card('Notices', `<div class="divide-y divide-slate-100">${notes.map(n => `<div class="p-4"><p class="text-sm font-medium">${ui.esc(n.title)}</p><p class="text-xs text-slate-500 mt-1">${ui.esc(n.date)}</p></div>`).join('')}</div>`, `<a href="${ui.url('notifications.html')}" class="text-xs text-[#2563eb]">View all</a>`)}</div>
      </div>`;
  };

  P.studentCourses = (el, user) => {
    const draw = () => {
      const mine = SMS.enrolledCourses(user.id); const ids = mine.map(c => c.id);
      const open = SMS.db.courses.filter(c => !ids.includes(c.id));
      const row = (c, enrolled) => ui.tr([`<p class="font-medium">${ui.esc(c.title)}</p><p class="text-xs text-slate-500">${ui.esc(c.code)}</p>`, ui.esc(SMS.dept(c.deptId).name), c.credits, ui.esc((SMS.lecturer(c.lecturerId) || {}).name || '—'),
        enrolled ? `<button data-drop="${c.id}" class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Drop</button>` : `<button data-add="${c.id}" class="text-xs px-3 py-1.5 rounded-lg bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Enrol</button>`]);
      const heads = ['Course', 'Department', 'CU', 'Lecturer', ''];
      el.innerHTML = ui.heading('My courses', `${SMS.credits(user.id)} credit units registered. Maximum load is 24.`) +
        `<div class="space-y-6">${ui.card('Registered courses', ui.table(heads, mine.map(c => row(c, true)), 'No registered courses.'))}${ui.card('Available to register', ui.table(heads, open.map(c => row(c, false)), 'No other courses available.'))}</div>`;
      el.querySelectorAll('[data-add]').forEach(b => b.onclick = () => {
        const c = SMS.course(b.dataset.add);
        if (SMS.credits(user.id) + c.credits > 24) return ui.toast('That would exceed the 24 credit unit limit.');
        SMS.db.enrollments.push({ studentId: user.id, courseId: c.id }); SMS.save(); ui.toast('Registered for ' + c.code); draw();
      });
      el.querySelectorAll('[data-drop]').forEach(b => b.onclick = () => {
        if (!ui.confirm('Drop this course? Any marks recorded for it will be removed.')) return;
        const id = b.dataset.drop; SMS.db.enrollments = SMS.db.enrollments.filter(e => !(e.studentId === user.id && e.courseId === id));
        SMS.db.grades = SMS.db.grades.filter(g => !(g.studentId === user.id && g.courseId === id)); SMS.save(); draw();
      });
      ui.icons();
    };
    draw();
  };

  P.studentResults = (el, user) => {
    const courses = SMS.enrolledCourses(user.id), gpa = SMS.gpa(user.id);
    el.innerHTML = ui.heading('Results', `Semester transcript for ${ui.esc(user.name)}`, `<button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:bg-slate-100"><i data-lucide="printer" class="w-4 h-4 mr-2"></i>Print</button>`) +
      `<div class="grid sm:grid-cols-3 gap-4 mb-6">${ui.stat('trending-up', 'Semester GPA', fmtGpa(gpa), classOf(gpa))}${ui.stat('book-open', 'Credit units', SMS.credits(user.id))}${ui.stat('award', 'Courses graded', courses.filter(c => SMS.result(user.id, c.id)).length + ' / ' + courses.length, '', 'gold')}</div>` +
      ui.card('2026 Semester 1', ui.table(['Code', 'Course', 'CU', 'Coursework /40', 'Exam /60', 'Total', 'Grade', 'Points'], courses.map(c => {
        const g = SMS.db.grades.find(x => x.studentId === user.id && x.courseId === c.id) || {}; const r = SMS.result(user.id, c.id);
        return ui.tr([ui.esc(c.code), ui.esc(c.title), c.credits, g.cw ?? '—', g.exam ?? '—', r ? r.total : '—', ui.gradeBadge(r), r ? r.points.toFixed(1) : '—']);
      }), 'No courses registered.')) +
      `<p class="text-xs text-slate-500 mt-4 max-w-3xl">Grading: ${SMS.scale.map(s => `${s[1]} ≥ ${s[0]}`).join(' · ')}. GPA is weighted by credit units.</p>`;
  };

  P.studentAttendance = (el, user) => {
    el.innerHTML = ui.heading('Attendance', 'You need at least 75% attendance in a course to sit its exam.') +
      ui.card('', ui.table(['Course', 'Sessions', 'Attended', 'Rate', 'Status'], SMS.enrolledCourses(user.id).map(c => {
        const recs = SMS.db.attendance.filter(a => a.courseId === c.id && user.id in a.marks); const got = recs.filter(a => a.marks[user.id]).length;
        const p = SMS.attendancePct(user.id, c.id);
        return ui.tr([`<p class="font-medium">${ui.esc(c.title)}</p><p class="text-xs text-slate-500">${ui.esc(c.code)}</p>`, recs.length, got, p == null ? '—' : `<div class="w-32">${ui.bar(p, ui.attTone(p))}<p class="text-xs text-slate-500 mt-1">${p}%</p></div>`, p == null ? ui.badge('No data') : p >= 75 ? ui.badge('Eligible', 'green') : ui.badge('At risk', 'red')]);
      }), 'No courses registered.'));
  };

  P.studentFees = (el, user) => {
    const draw = () => {
      const f = SMS.feeSummary(user.id); const pct = f.total ? Math.round(100 * f.paid / f.total) : 0;
      el.innerHTML = ui.heading('Fees', 'Tuition and payment history for this semester.') +
        `<div class="grid sm:grid-cols-3 gap-4 mb-6">${ui.stat('receipt', 'Total fees', ui.money(f.total))}${ui.stat('check-circle-2', 'Paid', ui.money(f.paid), pct + '% cleared')}${ui.stat('wallet', 'Balance', ui.money(f.balance), f.balance <= 0 ? 'Fully paid' : 'Outstanding', 'gold')}</div>
        <div class="grid lg:grid-cols-3 gap-6"><div class="lg:col-span-2">${ui.card('Payment history', ui.table(['Date', 'Reference', 'Method', 'Amount'], f.payments.slice().sort((a, b) => b.date.localeCompare(a.date)).map(p => ui.tr([p.date, ui.esc(p.ref), ui.esc(p.method), ui.money(p.amount)])), 'No payments recorded.'))}</div>
        <div>${ui.card('Record a payment', `<form id="pay-form" class="p-5 space-y-4">${ui.field('Amount (UGX)', 'amount', '', { type: 'number', required: true, min: 1000, max: Math.max(f.balance, 0) || 1 })}${ui.field('Method', 'method', 'Mobile Money', { options: ['Mobile Money', 'Bank deposit', 'Cash at bursar'] })}${ui.field('Transaction reference', 'ref', '', { required: true })}
          <button ${f.balance <= 0 ? 'disabled' : ''} class="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-[#2563eb] hover:bg-[#1d4ed8] disabled:opacity-50">Submit payment</button></form>`)}</div></div>`;
      el.querySelector('#pay-form').addEventListener('submit', e => {
        e.preventDefault(); const v = ui.formData(e.target);
        SMS.db.payments.push({ id: SMS.uid('p'), studentId: user.id, amount: Number(v.amount), date: new Date().toISOString().slice(0, 10), method: v.method, ref: v.ref });
        SMS.save(); ui.toast('Payment recorded'); draw(); ui.icons();
      });
      ui.icons();
    };
    draw();
  };

  // shared by all roles
  P.notifications = (el, user) => {
    const draw = () => {
      const mine = SMS.db.announcements.filter(n => user.role === 'admin' || n.audience === 'All' || (n.audience === 'Students' && user.role === 'student') || (n.audience === 'Staff' && user.role === 'staff')).sort((a, b) => b.date.localeCompare(a.date));
      const form = user.role === 'admin' ? ui.card('Post an announcement', `<form id="ann-form" class="p-5 grid sm:grid-cols-2 gap-4">${ui.field('Title', 'title', '', { required: true, span2: true })}${ui.field('Audience', 'audience', 'All', { options: ['All', 'Students', 'Staff'] })}${ui.field('Message', 'body', '', { textarea: true, required: true, span2: true, rows: 3 })}<div class="sm:col-span-2"><button class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Publish</button></div></form>`) : '';
      el.innerHTML = ui.heading(user.role === 'admin' ? 'Announcements' : 'Notices', 'News from the Registry, departments and the bursar.') +
        `<div class="max-w-3xl space-y-4">${form}${mine.map(n => `<div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm"><div class="flex items-start justify-between gap-3"><div><p class="font-semibold">${ui.esc(n.title)}</p><p class="text-xs text-slate-500 mt-1">${ui.esc(n.date)} · ${ui.badge(n.audience, n.audience === 'All' ? 'blue' : 'gold')}</p></div>${user.role === 'admin' ? `<button data-del="${n.id}" class="text-xs text-red-700 hover:underline">Delete</button>` : ''}</div><p class="text-sm text-slate-600 mt-3">${ui.esc(n.body)}</p></div>`).join('') || '<p class="text-sm text-slate-500">No notices.</p>'}</div>`;
      const f = el.querySelector('#ann-form');
      if (f) f.addEventListener('submit', e => { e.preventDefault(); const v = ui.formData(e.target); SMS.db.announcements.push({ id: SMS.uid('n'), title: v.title, body: v.body, audience: v.audience, date: new Date().toISOString().slice(0, 10) }); SMS.save(); ui.toast('Published'); draw(); });
      el.querySelectorAll('[data-del]').forEach(b => b.onclick = () => { if (!ui.confirm('Delete this announcement?')) return; SMS.db.announcements = SMS.db.announcements.filter(n => n.id !== b.dataset.del); SMS.save(); draw(); });
      ui.icons();
    };
    draw();
  };

  P.profile = (el, user) => {
    const isStu = user.role === 'student';
    el.innerHTML = ui.heading('My account', 'Update your contact details. Academic fields are managed by the Registry.') +
      ui.formShell(
        ui.field('Full name', 'name', user.name, { required: true }) + ui.field('Email', 'email', user.email, { type: 'email', required: true }) +
        (isStu ? ui.field('Phone', 'phone', user.phone) + ui.field('Home address', 'address', user.address) + ui.field('Guardian', 'guardian', user.guardian) +
          `<div><p class="block text-sm font-medium text-slate-600 mb-2">Registration no.</p><p class="input-field bg-slate-50">${ui.esc(user.regNo)}</p></div><div><p class="block text-sm font-medium text-slate-600 mb-2">Programme</p><p class="input-field bg-slate-50">${ui.esc(user.programme)} · Year ${user.year}</p></div>` : ''),
        ui.url(ui.home(user.role)), 'Save changes');
    el.querySelector('#main-form').addEventListener('submit', e => {
      e.preventDefault(); const v = ui.formData(e.target);
      const rec = isStu ? SMS.student(user.id) : SMS.lecturer(user.id) || SMS.db.users.find(u => u.id === user.id);
      Object.assign(rec, isStu ? { name: v.name, email: v.email, phone: v.phone, address: v.address, guardian: v.guardian } : { name: v.name, email: v.email });
      SMS.save(); ui.toast('Profile updated'); setTimeout(() => location.reload(), 500);
    });
  };
})();
