/* Lecturer pages. */
(function () {
  const P = SMS.pages; const ui = SMS.ui;
  const myCourses = u => SMS.db.courses.filter(c => c.lecturerId === u.id);
  const picker = (courses, sel) => `<select id="course-pick" class="input-field max-w-sm">${courses.map(c => `<option value="${c.id}"${c.id === sel ? ' selected' : ''}>${ui.esc(c.code)} — ${ui.esc(c.title)}</option>`).join('')}</select>`;

  P.staffDashboard = (el, user) => {
    const cs = myCourses(user); const total = new Set(cs.flatMap(c => SMS.studentsIn(c.id).map(s => s.id))).size;
    const graded = cs.reduce((a, c) => a + SMS.studentsIn(c.id).filter(s => SMS.result(s.id, c.id)).length, 0);
    const all = cs.reduce((a, c) => a + SMS.studentsIn(c.id).length, 0);
    el.innerHTML = ui.heading(`Hello, ${ui.esc(user.name)}`, 'Your teaching load for 2026 Semester 1.') +
      `<div class="grid sm:grid-cols-3 gap-4 mb-6">${ui.stat('book-open', 'Courses', cs.length)}${ui.stat('users', 'Students taught', total)}${ui.stat('award', 'Marks completed', all ? Math.round(100 * graded / all) + '%' : '—', graded + ' of ' + all + ' entries', 'gold')}</div>` +
      ui.card('My courses', ui.table(['Course', 'Department', 'Students', 'Avg attendance', 'Avg mark', ''], cs.map(c => {
        const st = SMS.studentsIn(c.id); const rs = st.map(s => SMS.result(s.id, c.id)).filter(Boolean);
        const recs = SMS.db.attendance.filter(a => a.courseId === c.id); let att = null;
        if (recs.length) { const t = recs.reduce((a, r) => a + Object.values(r.marks).length, 0); const p = recs.reduce((a, r) => a + Object.values(r.marks).filter(Boolean).length, 0); att = t ? Math.round(100 * p / t) : null; }
        return ui.tr([`<p class="font-medium">${ui.esc(c.title)}</p><p class="text-xs text-slate-500">${ui.esc(c.code)} · ${c.credits} CU</p>`, ui.esc(SMS.dept(c.deptId).name), st.length, att == null ? '—' : att + '%', rs.length ? (rs.reduce((a, r) => a + r.total, 0) / rs.length).toFixed(1) : '—',
          `<a class="text-xs text-[#2563eb] mr-3" href="${ui.url('staff/attendance.html')}?course=${c.id}">Attendance</a><a class="text-xs text-[#2563eb]" href="${ui.url('staff/grades.html')}?course=${c.id}">Grades</a>`]);
      }), 'No courses are assigned to you.'));
  };

  P.staffAttendance = (el, user) => {
    const cs = myCourses(user); if (!cs.length) { el.innerHTML = ui.heading('Attendance', '') + '<p class="text-sm text-slate-500">No courses assigned.</p>'; return; }
    let cid = ui.qs('course') && cs.some(c => c.id === ui.qs('course')) ? ui.qs('course') : cs[0].id;
    const today = new Date().toISOString().slice(0, 10);
    const draw = (date) => {
      date = date || today; const st = SMS.studentsIn(cid); const rec = SMS.db.attendance.find(a => a.courseId === cid && a.date === date);
      el.innerHTML = ui.heading('Attendance', 'Mark who attended the session.') +
        `<div class="flex flex-wrap gap-4 mb-5 items-end"><div><label class="block text-sm font-medium text-slate-600 mb-2">Course</label>${picker(cs, cid)}</div><div><label class="block text-sm font-medium text-slate-600 mb-2">Session date</label><input type="date" id="att-date" value="${date}" class="input-field"></div></div>` +
        ui.card('', `<form id="att-form">${ui.table(['Reg no.', 'Student', 'Present', 'Course rate'], st.map(s => ui.tr([ui.esc(s.regNo), `<span class="font-medium">${ui.esc(s.name)}</span>`, `<input type="checkbox" name="${s.id}" ${(rec ? rec.marks[s.id] : true) ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 text-[#2563eb]">`, (SMS.attendancePct(s.id, cid) ?? '—') + (SMS.attendancePct(s.id, cid) == null ? '' : '%')])), 'No students enrolled.')}
        <div class="px-4 py-4 border-t border-slate-200 flex justify-between items-center"><p class="text-xs text-slate-500">${rec ? 'Editing an existing record.' : 'New session — everyone starts as present.'}</p><button class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Save attendance</button></div></form>`);
      el.querySelector('#course-pick').onchange = e => { cid = e.target.value; draw(el.querySelector('#att-date').value); };
      el.querySelector('#att-date').onchange = e => draw(e.target.value);
      el.querySelector('#att-form').addEventListener('submit', e => {
        e.preventDefault(); const marks = {}; st.forEach(s => marks[s.id] = e.target.elements[s.id].checked);
        if (rec) rec.marks = marks; else SMS.db.attendance.push({ id: SMS.uid('a'), courseId: cid, date, marks });
        SMS.save(); ui.toast('Attendance saved'); draw(date);
      });
      ui.icons();
    };
    draw();
  };

  P.staffGrades = (el, user) => {
    const cs = myCourses(user); if (!cs.length) { el.innerHTML = ui.heading('Grades', '') + '<p class="text-sm text-slate-500">No courses assigned.</p>'; return; }
    let cid = ui.qs('course') && cs.some(c => c.id === ui.qs('course')) ? ui.qs('course') : cs[0].id;
    const draw = () => {
      const st = SMS.studentsIn(cid);
      el.innerHTML = ui.heading('Grades', 'Coursework is out of 40 and the exam out of 60.') +
        `<div class="mb-5"><label class="block text-sm font-medium text-slate-600 mb-2">Course</label>${picker(cs, cid)}</div>` +
        ui.card('', `<form id="grade-form">${ui.table(['Reg no.', 'Student', 'Coursework /40', 'Exam /60', 'Total', 'Grade'], st.map(s => {
          const g = SMS.db.grades.find(x => x.studentId === s.id && x.courseId === cid) || {};
          return ui.tr([ui.esc(s.regNo), `<span class="font-medium">${ui.esc(s.name)}</span>`,
            `<input type="number" min="0" max="40" name="cw_${s.id}" value="${g.cw ?? ''}" class="input-field !w-24 !py-2">`, `<input type="number" min="0" max="60" name="ex_${s.id}" value="${g.exam ?? ''}" class="input-field !w-24 !py-2">`,
            `<span data-total="${s.id}">${SMS.result(s.id, cid) ? SMS.result(s.id, cid).total : '—'}</span>`, `<span data-grade="${s.id}">${ui.gradeBadge(SMS.result(s.id, cid))}</span>`]);
        }), 'No students enrolled.')}<div class="px-4 py-4 border-t border-slate-200 flex justify-end"><button class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#2563eb] hover:bg-[#1d4ed8]">Save marks</button></div></form>`);
      el.querySelector('#course-pick').onchange = e => { cid = e.target.value; draw(); };
      const form = el.querySelector('#grade-form');
      const live = s => { const cw = form.elements['cw_' + s.id].value, ex = form.elements['ex_' + s.id].value;
        const ok = cw !== '' && ex !== ''; const t = Number(cw) + Number(ex);
        form.querySelector(`[data-total="${s.id}"]`).textContent = ok ? t : '—';
        form.querySelector(`[data-grade="${s.id}"]`).innerHTML = ui.gradeBadge(ok ? Object.assign({ total: t }, SMS.gradeOf(t)) : null); };
      st.forEach(s => ['cw_', 'ex_'].forEach(p => form.elements[p + s.id].addEventListener('input', () => live(s))));
      form.addEventListener('submit', e => {
        e.preventDefault();
        for (const s of st) {
          const cw = form.elements['cw_' + s.id].value, ex = form.elements['ex_' + s.id].value;
          if ((cw !== '' && (cw < 0 || cw > 40)) || (ex !== '' && (ex < 0 || ex > 60))) return ui.toast('Marks out of range for ' + s.name);
        }
        st.forEach(s => {
          const cw = form.elements['cw_' + s.id].value, ex = form.elements['ex_' + s.id].value;
          let g = SMS.db.grades.find(x => x.studentId === s.id && x.courseId === cid);
          if (!g) { g = { studentId: s.id, courseId: cid }; SMS.db.grades.push(g); }
          g.cw = cw === '' ? null : Number(cw); g.exam = ex === '' ? null : Number(ex);
        });
        SMS.save(); ui.toast('Marks saved');
      });
      ui.icons();
    };
    draw();
  };
})();
