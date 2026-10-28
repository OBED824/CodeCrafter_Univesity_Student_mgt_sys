/* Student Management System — data layer (localStorage, no backend).
   Swap this file for API calls when a real backend exists. */
(function () {
  const KEY = 'sms.v1', SKEY = 'sms.session';

  function rng(seed) { // mulberry32
    return function () { seed |= 0; seed = (seed + 0x6D2B79F5) | 0; let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
      t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t; return ((t ^ (t >>> 14)) >>> 0) / 4294967296; };
  }

  function seed() {
    const r = rng(2026);
    const pick = a => a[Math.floor(r() * a.length)];
    const between = (a, b) => Math.round(a + r() * (b - a));

    const departments = [
      { id: 'd1', code: 'CS', name: 'Computer Science', head: 'Dr. Grace Nakato' },
      { id: 'd2', code: 'BA', name: 'Business Administration', head: 'Mr. Peter Okello' },
      { id: 'd3', code: 'AF', name: 'Accounting & Finance', head: 'Ms. Sarah Kintu' },
      { id: 'd4', code: 'PL', name: 'Procurement & Logistics', head: 'Dr. David Mugisha' },
      { id: 'd5', code: 'MK', name: 'Marketing', head: 'Ms. Ruth Atim' },
    ];
    const users = [
      { id: 'u1', name: 'Dr. Grace Nakato', email: 'grace.nakato@campus.ac.ug', role: 'staff' },
      { id: 'u2', name: 'Mr. Peter Okello', email: 'peter.okello@campus.ac.ug', role: 'staff' },
      { id: 'u3', name: 'Ms. Sarah Kintu', email: 'sarah.kintu@campus.ac.ug', role: 'staff' },
      { id: 'u4', name: 'Dr. David Mugisha', email: 'david.mugisha@campus.ac.ug', role: 'staff' },
      { id: 'u5', name: 'Ms. Ruth Atim', email: 'ruth.atim@campus.ac.ug', role: 'staff' },
      { id: 'u9', name: 'Registry Admin', email: 'admin@campus.ac.ug', role: 'admin' },
    ];
    const C = (id, code, title, deptId, credits, lecturerId) => ({ id, code, title, deptId, credits, lecturerId, semester: '2026 Semester 1' });
    const courses = [
      C('c1', 'CS101', 'Introduction to Programming', 'd1', 4, 'u1'), C('c2', 'CS201', 'Data Structures', 'd1', 4, 'u1'),
      C('c3', 'CS210', 'Database Systems', 'd1', 3, 'u1'), C('c4', 'CS305', 'Web Engineering', 'd1', 3, 'u1'),
      C('c5', 'BA101', 'Principles of Management', 'd2', 3, 'u2'), C('c6', 'BA220', 'Organisational Behaviour', 'd2', 3, 'u2'),
      C('c7', 'AF110', 'Financial Accounting', 'd3', 4, 'u3'), C('c8', 'AF230', 'Cost Accounting', 'd3', 3, 'u3'),
      C('c9', 'PL120', 'Supply Chain Basics', 'd4', 3, 'u4'), C('c10', 'PL240', 'Procurement Law', 'd4', 3, 'u4'),
      C('c11', 'MK101', 'Marketing Principles', 'd5', 3, 'u5'), C('c12', 'MK250', 'Consumer Behaviour', 'd5', 3, 'u5'),
      C('c13', 'GS100', 'Communication Skills', 'd2', 2, 'u2'),
    ];
    const first = ['Aisha', 'Brian', 'Catherine', 'Denis', 'Esther', 'Francis', 'Gloria', 'Hassan', 'Irene', 'Joseph', 'Kevin', 'Lillian', 'Moses', 'Naomi', 'Oscar', 'Patricia', 'Quinto', 'Rebecca', 'Samuel', 'Tracy', 'Umar', 'Viola', 'Wilson', 'Yvonne'];
    const last = ['Namuli', 'Ssemakula', 'Auma', 'Byaruhanga', 'Nabirye', 'Opio', 'Kyomuhendo', 'Mukasa', 'Tumusiime', 'Wasswa', 'Akello', 'Nsubuga'];
    const students = []; const enrollments = []; const grades = []; const fees = {}; const payments = [];
    const progs = { d1: 'BSc Computer Science', d2: 'BBA Business Administration', d3: 'BCom Accounting & Finance', d4: 'BSc Procurement & Logistics', d5: 'BBA Marketing' };
    first.forEach((fn, i) => {
      const deptId = 'd' + (1 + (i % 5)); const ln = last[(i * 5) % last.length];
      const id = 's' + (i + 1);
      students.push({ id, regNo: '25/U/' + (1001 + i), name: fn + ' ' + ln, email: (fn + '.' + ln).toLowerCase() + '@campus.ac.ug',
        phone: '+2567' + between(10000000, 99999999), gender: i % 2 ? 'Male' : 'Female', dob: '200' + between(1, 6) + '-0' + between(1, 9) + '-1' + between(0, 9),
        deptId, programme: progs[deptId], year: between(1, 3), status: i === 7 ? 'Suspended' : (i === 11 ? 'Deferred' : 'Active'),
        guardian: pick(first) + ' ' + ln, address: pick(['Kampala', 'Wakiso', 'Mukono', 'Entebbe', 'Jinja']), joined: '2025-08-' + (10 + (i % 15)) });
      const own = courses.filter(c => c.deptId === deptId && c.id !== 'c13').slice(0, 4);
      own.concat(courses.find(c => c.id === 'c13')).forEach(c => {
        enrollments.push({ studentId: id, courseId: c.id });
        grades.push({ studentId: id, courseId: c.id, cw: between(18, 38), exam: between(22, 58) });
      });
      fees[id] = { total: 1850000 };
      const paid = pick([0, 0.4, 0.6, 1, 1]) * 1850000;
      if (paid) payments.push({ id: 'p' + id, studentId: id, amount: paid, date: '2026-09-0' + between(1, 9), method: pick(['Mobile Money', 'Bank deposit']), ref: 'TX' + between(100000, 999999) });
    });
    const attendance = [];
    courses.forEach(c => {
      const ids = enrollments.filter(e => e.courseId === c.id).map(e => e.studentId);
      for (let d = 0; d < 6; d++) {
        const marks = {}; ids.forEach(s => marks[s] = r() < 0.82);
        attendance.push({ id: 'a' + c.id + d, courseId: c.id, date: '2026-10-' + String(5 + d * 3).padStart(2, '0'), marks });
      }
    });
    const announcements = [
      { id: 'n1', title: 'Semester 1 examinations timetable released', body: 'The examination timetable is now available at the Registry. Check your course units and report clashes by Friday.', date: '2026-10-26', audience: 'All' },
      { id: 'n2', title: 'Tuition balance deadline', body: 'Students with outstanding balances should clear at least 60% before sitting examinations.', date: '2026-10-22', audience: 'Students' },
      { id: 'n3', title: 'Coursework marks submission', body: 'Lecturers should submit coursework marks via the Grades page by 6 November.', date: '2026-10-20', audience: 'Staff' },
    ];
    return { departments, users, courses, students, enrollments, grades, fees, payments, attendance, announcements };
  }

  function load() {
    try { const raw = localStorage.getItem(KEY); if (raw) return JSON.parse(raw); } catch (e) {}
    const d = seed(); save(d); return d;
  }
  function save(d) { try { localStorage.setItem(KEY, JSON.stringify(d)); } catch (e) {} SMS.db = d; }

  const SMS = window.SMS = { db: null };
  SMS.db = load();
  SMS.save = () => save(SMS.db);
  SMS.reset = () => { try { localStorage.removeItem(KEY); localStorage.removeItem(SKEY); } catch (e) {} SMS.db = seed(); save(SMS.db); };
  SMS.uid = p => p + Date.now().toString(36) + Math.random().toString(36).slice(2, 5);

  // session
  SMS.session = () => { try { return JSON.parse(localStorage.getItem(SKEY)); } catch (e) { return null; } };
  SMS.login = s => { try { localStorage.setItem(SKEY, JSON.stringify(s)); } catch (e) {} };
  SMS.logout = () => { try { localStorage.removeItem(SKEY); } catch (e) {} };
  SMS.currentUser = () => {
    const s = SMS.session(); if (!s) return null;
    const u = s.role === 'student' ? SMS.db.students.find(x => x.id === s.id) : SMS.db.users.find(x => x.id === s.id);
    return u ? Object.assign({}, u, { role: s.role }) : null;
  };

  // lookups
  SMS.dept = id => SMS.db.departments.find(d => d.id === id) || { name: '—', code: '—' };
  SMS.course = id => SMS.db.courses.find(c => c.id === id);
  SMS.student = id => SMS.db.students.find(s => s.id === id);
  SMS.lecturer = id => SMS.db.users.find(u => u.id === id);
  SMS.enrolledCourses = sid => SMS.db.enrollments.filter(e => e.studentId === sid).map(e => SMS.course(e.courseId)).filter(Boolean);
  SMS.studentsIn = cid => SMS.db.enrollments.filter(e => e.courseId === cid).map(e => SMS.student(e.studentId)).filter(Boolean);

  // grading (coursework /40 + exam /60)
  const SCALE = [[80, 'A', 5], [75, 'B+', 4.5], [70, 'B', 4], [65, 'C+', 3.5], [60, 'C', 3], [55, 'D+', 2.5], [50, 'D', 2], [40, 'E', 1], [0, 'F', 0]];
  SMS.scale = SCALE;
  SMS.gradeOf = total => { const g = SCALE.find(x => total >= x[0]); return { letter: g[1], points: g[2] }; };
  SMS.result = (sid, cid) => {
    const g = SMS.db.grades.find(x => x.studentId === sid && x.courseId === cid);
    if (!g || g.cw == null || g.exam == null || g.cw === '' || g.exam === '') return null;
    const total = Number(g.cw) + Number(g.exam); return Object.assign({ total }, SMS.gradeOf(total));
  };
  SMS.gpa = sid => {
    let pts = 0, cr = 0;
    SMS.enrolledCourses(sid).forEach(c => { const r = SMS.result(sid, c.id); if (r) { pts += r.points * c.credits; cr += c.credits; } });
    return cr ? pts / cr : null;
  };
  SMS.credits = sid => SMS.enrolledCourses(sid).reduce((a, c) => a + c.credits, 0);
  SMS.attendancePct = (sid, cid) => {
    const recs = SMS.db.attendance.filter(a => (!cid || a.courseId === cid) && sid in a.marks);
    if (!recs.length) return null;
    return Math.round(100 * recs.filter(a => a.marks[sid]).length / recs.length);
  };
  SMS.feeSummary = sid => {
    const total = (SMS.db.fees[sid] || { total: 0 }).total;
    const pays = SMS.db.payments.filter(p => p.studentId === sid);
    const paid = pays.reduce((a, p) => a + Number(p.amount), 0);
    return { total, paid, balance: total - paid, payments: pays };
  };
  SMS.removeStudent = id => {
    const d = SMS.db;
    d.students = d.students.filter(s => s.id !== id);
    d.enrollments = d.enrollments.filter(e => e.studentId !== id);
    d.grades = d.grades.filter(g => g.studentId !== id);
    d.payments = d.payments.filter(p => p.studentId !== id);
    delete d.fees[id];
    d.attendance.forEach(a => delete a.marks[id]);
    SMS.save();
  };
  SMS.nextRegNo = () => {
    const n = Math.max(1000, ...SMS.db.students.map(s => parseInt(String(s.regNo).split('/').pop(), 10) || 0)) + 1;
    return '26/U/' + n;
  };
})();
