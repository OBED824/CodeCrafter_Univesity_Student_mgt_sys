/* Administrator pages: dashboard, students, courses, departments, fees. */
(function () {
  const P = SMS.pages; const ui = SMS.ui;
  const A = (p) => ui.url('admin/' + p);
  const deptOpts = () => SMS.db.departments.map(d => [d.id, d.name]);
  const lecOpts = () => SMS.db.users.filter(u => u.role === 'staff').map(u => [u.id, u.name]);
  const iconBtn = (href, icon, title) => `<a href="${href}" title="${title}" class="inline-flex p-1.5 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-[#2563eb]"><i data-lucide="${icon}" class="w-4 h-4"></i></a>`;
  const delBtn = id => `<button data-del="${id}" title="Delete" class="inline-flex p-1.5 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-700"><i data-lucide="trash-2" class="w-4 h-4"></i></button>`;

  P.adminDashboard = (el) => {
    const d = SMS.db; const collected = d.payments.reduce((a, p) => a + Number(p.amount), 0);
    const billed = Object.values(d.fees).reduce((a, f) => a + f.total, 0);
    const max = Math.max(1, ...d.departments.map(x => d.students.filter(s => s.deptId === x.id).length));
    const recent = d.students.slice().sort((a, b) => b.joined.localeCompare(a.joined)).slice(0, 5);
    const atRisk = d.students.filter(s => { const a = SMS.attendancePct(s.id); return a != null && a < 75; });
    el.innerHTML = ui.heading('Administration', 'Overview of students, courses and fee collection.', ui.btn('Add student', A('students/create.html'), 'user-plus')) +
      `<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">${ui.stat('users', 'Students', d.students.length, d.students.filter(s => s.status === 'Active').length + ' active')}${ui.stat('book-open', 'Courses', d.courses.length, d.departments.length + ' departments')}${ui.stat('wallet', 'Fees collected', ui.money(collected), billed ? Math.round(100 * collected / billed) + '% of billed' : '', 'gold')}${ui.stat('alert-triangle', 'Attendance at risk', atRisk.length, 'students below 75%', 'gold')}</div>
      <div class="grid lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">${ui.card('Students by department', `<div class="p-5 space-y-4">${d.departments.map(x => { const n = d.students.filter(s => s.deptId === x.id).length; return `<div><div class="flex justify-between text-sm mb-1"><span>${ui.esc(x.name)}</span><span class="text-slate-500">${n}</span></div>${ui.bar(100 * n / max)}</div>`; }).join('')}</div>`)}</div>
        <div class="space-y-4">${[['departments/index.html', 'building-2', 'Departments', d.departments.length + ' departments'], ['courses/index.html', 'book-open', 'Courses', d.courses.length + ' course units'], ['fees.html', 'wallet', 'Fees', ui.money(billed - collected) + ' outstanding']].map(x => `<a href="${A(x[0])}" class="block bg-white border border-slate-200 rounded-xl p-5 shadow-sm hover:border-blue-200"><div class="flex items-center gap-3 mb-2"><i data-lucide="${x[1]}" class="w-5 h-5 text-[#2563eb]"></i><h3 class="font-semibold">${x[2]}</h3></div><p class="text-sm text-slate-500">${x[3]}</p></a>`).join('')}</div></div>` +
      ui.card('Recently registered', ui.table(['Reg no.', 'Student', 'Programme', 'Joined', 'Status'], recent.map(s => ui.tr([ui.esc(s.regNo), `<a class="font-medium text-[#2563eb]" href="${A('students/show.html')}?id=${s.id}">${ui.esc(s.name)}</a>`, ui.esc(s.programme), s.joined, ui.statusBadge(s.status)]))), `<a href="${A('students/index.html')}" class="text-xs text-[#2563eb]">View all</a>`);
  };

  // ---------- students ----------
  P.studentsIndex = (el) => {
    const draw = () => {
      const q = (ui.qs('q') || '').toLowerCase(), dep = ui.qs('dept') || '', st = ui.qs('status') || '';
      const list = SMS.db.students.filter(s => (!q || (s.name + s.regNo + s.email).toLowerCase().includes(q)) && (!dep || s.deptId === dep) && (!st || s.status === st));
      el.innerHTML = ui.heading('Students', `${list.length} of ${SMS.db.students.length} students`, ui.btn('Add student', A('students/create.html'), 'user-plus')) +
        `<form class="flex flex-wrap gap-3 mb-5"><input name="q" value="${ui.esc(q)}" placeholder="Search name, reg no. or email" class="input-field !w-72"><select name="dept" class="input-field !w-56"><option value="">All departments</option>${deptOpts().map(([v, t]) => `<option value="${v}"${v === dep ? ' selected' : ''}>${ui.esc(t)}</option>`).join('')}</select><select name="status" class="input-field !w-44"><option value="">Any status</option>${['Active', 'Deferred', 'Suspended', 'Graduated'].map(s => `<option${s === st ? ' selected' : ''}>${s}</option>`).join('')}</select><button class="px-4 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:bg-slate-100">Filter</button></form>` +
        ui.card('', ui.table(['Reg no.', 'Student', 'Department', 'Year', 'GPA', 'Status', ''], list.map(s => { const g = SMS.gpa(s.id);
          return ui.tr([ui.esc(s.regNo), `<p class="font-medium">${ui.esc(s.name)}</p><p class="text-xs text-slate-500">${ui.esc(s.email)}</p>`, ui.esc(SMS.dept(s.deptId).name), s.year, g == null ? '—' : g.toFixed(2), ui.statusBadge(s.status),
            `<div class="flex justify-end">${iconBtn(A('students/show.html') + '?id=' + s.id, 'eye', 'View')}${iconBtn(A('students/edit.html') + '?id=' + s.id, 'pencil', 'Edit')}${delBtn(s.id)}</div>`]); }), 'No students match your filters.'));
      el.querySelectorAll('[data-del]').forEach(b => b.onclick = () => { const s = SMS.student(b.dataset.del); if (!ui.confirm(`Delete ${s.name}? Their enrolments, marks and payments will be removed.`)) return; SMS.removeStudent(s.id); ui.toast('Student deleted'); draw(); });
      ui.icons();
    };
    draw();
  };

  const studentForm = (s, title, sub) => ui.formShell(
    ui.field('Full name', 'name', s.name, { required: true }) + ui.field('Registration no.', 'regNo', s.regNo, { required: true }) +
    ui.field('Email', 'email', s.email, { type: 'email', required: true }) + ui.field('Phone', 'phone', s.phone) +
    ui.field('Gender', 'gender', s.gender, { options: ['Female', 'Male'] }) + ui.field('Date of birth', 'dob', s.dob, { type: 'date' }) +
    ui.field('Department', 'deptId', s.deptId, { options: deptOpts(), required: true }) + ui.field('Programme', 'programme', s.programme, { required: true }) +
    ui.field('Year of study', 'year', s.year, { options: [1, 2, 3, 4, 5] }) + ui.field('Status', 'status', s.status, { options: ['Active', 'Deferred', 'Suspended', 'Graduated'] }) +
    ui.field('Guardian', 'guardian', s.guardian) + ui.field('Address', 'address', s.address), A('students/index.html'), 'Save student');

  P.studentsCreate = (el) => {
    el.innerHTML = ui.heading('Add student', 'Create a new student record.') + studentForm({ regNo: SMS.nextRegNo(), gender: 'Female', deptId: SMS.db.departments[0].id, programme: 'BSc ' + SMS.db.departments[0].name, year: 1, status: 'Active' });
    el.querySelector('#main-form').addEventListener('submit', e => {
      e.preventDefault(); const v = ui.formData(e.target);
      if (SMS.db.students.some(s => s.regNo === v.regNo)) return ui.toast('That registration number is already used.');
      if (SMS.db.students.some(s => s.email.toLowerCase() === v.email.toLowerCase())) return ui.toast('That email is already used.');
      const id = SMS.uid('s'); SMS.db.students.push(Object.assign({ id, joined: new Date().toISOString().slice(0, 10) }, v, { year: Number(v.year) }));
      SMS.db.fees[id] = { total: 1850000 }; SMS.save(); location.href = A('students/show.html') + '?id=' + id;
    });
  };
  P.studentsEdit = (el) => {
    const s = SMS.student(ui.qs('id')); if (!s) { el.innerHTML = ui.heading('Student not found', '', ui.btn('Back', A('students/index.html'), 'arrow-left', 'ghost')); return; }
    el.innerHTML = ui.heading('Edit student', ui.esc(s.name)) + studentForm(s);
    el.querySelector('#main-form').addEventListener('submit', e => {
      e.preventDefault(); const v = ui.formData(e.target);
      if (SMS.db.students.some(x => x.id !== s.id && x.regNo === v.regNo)) return ui.toast('That registration number is already used.');
      if (SMS.db.students.some(x => x.id !== s.id && x.email.toLowerCase() === v.email.toLowerCase())) return ui.toast('That email is already used.');
      Object.assign(s, v, { year: Number(v.year) }); SMS.save(); location.href = A('students/show.html') + '?id=' + s.id;
    });
  };
  P.studentsShow = (el) => {
    const s = SMS.student(ui.qs('id')); if (!s) { el.innerHTML = ui.heading('Student not found', '', ui.btn('Back', A('students/index.html'), 'arrow-left', 'ghost')); return; }
    const g = SMS.gpa(s.id), f = SMS.feeSummary(s.id), att = SMS.attendancePct(s.id);
    const info = [['Email', s.email], ['Phone', s.phone || '—'], ['Gender', s.gender], ['Date of birth', s.dob || '—'], ['Guardian', s.guardian || '—'], ['Address', s.address || '—'], ['Joined', s.joined]];
    el.innerHTML = ui.heading(ui.esc(s.name), `${ui.esc(s.regNo)} · ${ui.esc(s.programme)} · Year ${s.year} ${ui.statusBadge(s.status)}`, ui.btn('Back', A('students/index.html'), 'arrow-left', 'ghost') + ui.btn('Edit', A('students/edit.html') + '?id=' + s.id, 'pencil')) +
      `<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">${ui.stat('trending-up', 'GPA', g == null ? '—' : g.toFixed(2))}${ui.stat('book-open', 'Credit units', SMS.credits(s.id))}${ui.stat('calendar-check', 'Attendance', att == null ? '—' : att + '%')}${ui.stat('wallet', 'Fee balance', ui.money(f.balance), '', 'gold')}</div>
      <div class="grid lg:grid-cols-3 gap-6"><div>${ui.card('Details', `<dl class="divide-y divide-slate-100">${info.map(([k, v]) => `<div class="px-5 py-3 flex justify-between gap-4 text-sm"><dt class="text-slate-500">${k}</dt><dd class="text-right">${ui.esc(v)}</dd></div>`).join('')}</dl>`)}</div>
      <div class="lg:col-span-2">${ui.card('Courses and results', ui.table(['Course', 'CU', 'Attendance', 'Result'], SMS.enrolledCourses(s.id).map(c => { const a = SMS.attendancePct(s.id, c.id); return ui.tr([`<p class="font-medium">${ui.esc(c.title)}</p><p class="text-xs text-slate-500">${ui.esc(c.code)}</p>`, c.credits, a == null ? '—' : a + '%', ui.gradeBadge(SMS.result(s.id, c.id))]); }), 'Not enrolled in any course.'))}</div></div>`;
  };

  // ---------- courses ----------
  P.coursesIndex = (el) => {
    const draw = () => {
      el.innerHTML = ui.heading('Courses', SMS.db.courses.length + ' course units', ui.btn('Add course', A('courses/create.html'), 'plus')) +
        ui.card('', ui.table(['Code', 'Title', 'Department', 'CU', 'Lecturer', 'Students', ''], SMS.db.courses.map(c => ui.tr([`<span class="font-medium">${ui.esc(c.code)}</span>`, ui.esc(c.title), ui.esc(SMS.dept(c.deptId).name), c.credits, ui.esc((SMS.lecturer(c.lecturerId) || {}).name || 'Unassigned'), SMS.studentsIn(c.id).length, `<div class="flex justify-end">${iconBtn(A('courses/edit.html') + '?id=' + c.id, 'pencil', 'Edit')}${delBtn(c.id)}</div>`]))));
      el.querySelectorAll('[data-del]').forEach(b => b.onclick = () => {
        const c = SMS.course(b.dataset.del); const n = SMS.studentsIn(c.id).length;
        if (!ui.confirm(`Delete ${c.code}?` + (n ? ` ${n} student(s) are enrolled and their marks will be removed.` : ''))) return;
        const d = SMS.db; d.courses = d.courses.filter(x => x.id !== c.id); d.enrollments = d.enrollments.filter(e => e.courseId !== c.id); d.grades = d.grades.filter(g => g.courseId !== c.id); d.attendance = d.attendance.filter(a => a.courseId !== c.id);
        SMS.save(); ui.toast('Course deleted'); draw();
      });
      ui.icons();
    };
    draw();
  };
  const courseForm = c => ui.formShell(ui.field('Course code', 'code', c.code, { required: true, placeholder: 'CS101' }) + ui.field('Credit units', 'credits', c.credits, { type: 'number', min: 1, max: 8, required: true }) +
    ui.field('Title', 'title', c.title, { required: true, span2: true }) + ui.field('Department', 'deptId', c.deptId, { options: deptOpts(), required: true }) + ui.field('Lecturer', 'lecturerId', c.lecturerId, { options: lecOpts() }) + ui.field('Semester', 'semester', c.semester || '2026 Semester 1', { required: true }), A('courses/index.html'), 'Save course');
  P.coursesCreate = (el) => {
    el.innerHTML = ui.heading('Add course', 'Create a new course unit.') + courseForm({ credits: 3, deptId: SMS.db.departments[0].id, lecturerId: lecOpts()[0][0] });
    el.querySelector('#main-form').addEventListener('submit', e => { e.preventDefault(); const v = ui.formData(e.target);
      if (SMS.db.courses.some(c => c.code.toLowerCase() === v.code.toLowerCase())) return ui.toast('That course code already exists.');
      SMS.db.courses.push(Object.assign({ id: SMS.uid('c') }, v, { credits: Number(v.credits) })); SMS.save(); location.href = A('courses/index.html'); });
  };
  P.coursesEdit = (el) => {
    const c = SMS.course(ui.qs('id')); if (!c) { el.innerHTML = ui.heading('Course not found', '', ui.btn('Back', A('courses/index.html'), 'arrow-left', 'ghost')); return; }
    el.innerHTML = ui.heading('Edit course', ui.esc(c.code)) + courseForm(c);
    el.querySelector('#main-form').addEventListener('submit', e => { e.preventDefault(); const v = ui.formData(e.target);
      if (SMS.db.courses.some(x => x.id !== c.id && x.code.toLowerCase() === v.code.toLowerCase())) return ui.toast('That course code already exists.');
      Object.assign(c, v, { credits: Number(v.credits) }); SMS.save(); location.href = A('courses/index.html'); });
  };

  // ---------- departments ----------
  P.departmentsIndex = (el) => {
    const draw = () => {
      el.innerHTML = ui.heading('Departments', SMS.db.departments.length + ' departments', ui.btn('Add department', A('departments/create.html'), 'plus')) +
        ui.card('', ui.table(['Code', 'Name', 'Head', 'Courses', 'Students', ''], SMS.db.departments.map(d => ui.tr([`<span class="font-medium">${ui.esc(d.code)}</span>`, ui.esc(d.name), ui.esc(d.head), SMS.db.courses.filter(c => c.deptId === d.id).length, SMS.db.students.filter(s => s.deptId === d.id).length, `<div class="flex justify-end">${iconBtn(A('departments/edit.html') + '?id=' + d.id, 'pencil', 'Edit')}${delBtn(d.id)}</div>`]))));
      el.querySelectorAll('[data-del]').forEach(b => b.onclick = () => {
        const d = SMS.dept(b.dataset.del);
        if (SMS.db.courses.some(c => c.deptId === d.id) || SMS.db.students.some(s => s.deptId === d.id)) return ui.toast('Move or delete its courses and students first.');
        if (!ui.confirm('Delete ' + d.name + '?')) return; SMS.db.departments = SMS.db.departments.filter(x => x.id !== d.id); SMS.save(); draw();
      });
      ui.icons();
    };
    draw();
  };
  const deptForm = d => ui.formShell(ui.field('Code', 'code', d.code, { required: true, placeholder: 'CS' }) + ui.field('Head of department', 'head', d.head, { required: true }) + ui.field('Name', 'name', d.name, { required: true, span2: true }), A('departments/index.html'), 'Save department');
  P.departmentsCreate = (el) => {
    el.innerHTML = ui.heading('Add department', '') + deptForm({});
    el.querySelector('#main-form').addEventListener('submit', e => { e.preventDefault(); SMS.db.departments.push(Object.assign({ id: SMS.uid('d') }, ui.formData(e.target))); SMS.save(); location.href = A('departments/index.html'); });
  };
  P.departmentsEdit = (el) => {
    const d = SMS.db.departments.find(x => x.id === ui.qs('id')); if (!d) { el.innerHTML = ui.heading('Department not found', '', ui.btn('Back', A('departments/index.html'), 'arrow-left', 'ghost')); return; }
    el.innerHTML = ui.heading('Edit department', ui.esc(d.name)) + deptForm(d);
    el.querySelector('#main-form').addEventListener('submit', e => { e.preventDefault(); Object.assign(d, ui.formData(e.target)); SMS.save(); location.href = A('departments/index.html'); });
  };

  // ---------- fees ----------
  P.adminFees = (el) => {
    const draw = () => {
      const rows = SMS.db.students.map(s => Object.assign({ s }, SMS.feeSummary(s.id)));
      const billed = rows.reduce((a, r) => a + r.total, 0), paid = rows.reduce((a, r) => a + r.paid, 0);
      el.innerHTML = ui.heading('Fees', 'Billing and payments for all students.') +
        `<div class="grid sm:grid-cols-3 gap-4 mb-6">${ui.stat('receipt', 'Billed', ui.money(billed))}${ui.stat('check-circle-2', 'Collected', ui.money(paid), billed ? Math.round(100 * paid / billed) + '%' : '')}${ui.stat('wallet', 'Outstanding', ui.money(billed - paid), rows.filter(r => r.balance > 0).length + ' students owe', 'gold')}</div>` +
        ui.card('Student balances', ui.table(['Student', 'Total', 'Paid', 'Balance', ''], rows.map(r => ui.tr([`<p class="font-medium">${ui.esc(r.s.name)}</p><p class="text-xs text-slate-500">${ui.esc(r.s.regNo)}</p>`, ui.money(r.total), ui.money(r.paid), r.balance > 0 ? `<span class="text-[#d97706] font-medium">${ui.money(r.balance)}</span>` : ui.badge('Cleared', 'green'), r.balance > 0 ? `<button data-pay="${r.s.id}" class="text-xs px-3 py-1.5 rounded-lg bg-[#2563eb] text-white hover:bg-[#1d4ed8]">Record payment</button>` : ''])))) +
        `<div id="modal"></div>`;
      el.querySelectorAll('[data-pay]').forEach(b => b.onclick = () => {
        const r = rows.find(x => x.s.id === b.dataset.pay);
        el.querySelector('#modal').innerHTML = `<div class="modal-backdrop"><form id="pay-form" class="bg-white rounded-xl p-6 w-full max-w-md space-y-4 shadow-xl"><h2 class="font-semibold">Payment — ${ui.esc(r.s.name)}</h2><p class="text-sm text-slate-500">Balance ${ui.money(r.balance)}</p>
          ${ui.field('Amount (UGX)', 'amount', r.balance, { type: 'number', required: true, min: 1, max: r.balance })}${ui.field('Method', 'method', 'Mobile Money', { options: ['Mobile Money', 'Bank deposit', 'Cash at bursar'] })}${ui.field('Reference', 'ref', '', { required: true })}
          <div class="flex justify-end gap-2"><button type="button" id="cancel" class="px-4 py-2 rounded-lg text-sm border border-slate-200">Cancel</button><button class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2563eb]">Save</button></div></form></div>`;
        el.querySelector('#cancel').onclick = () => el.querySelector('#modal').innerHTML = '';
        el.querySelector('#pay-form').addEventListener('submit', e => { e.preventDefault(); const v = ui.formData(e.target);
          SMS.db.payments.push({ id: SMS.uid('p'), studentId: r.s.id, amount: Number(v.amount), date: new Date().toISOString().slice(0, 10), method: v.method, ref: v.ref }); SMS.save(); ui.toast('Payment recorded'); draw(); });
      });
      ui.icons();
    };
    draw();
  };
})();
