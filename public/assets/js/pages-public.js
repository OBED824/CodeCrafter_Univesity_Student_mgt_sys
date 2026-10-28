/* Public pages: sign in, register, password reset, contact. */
(function () {
  const P = SMS.pages = SMS.pages || {}; const ui = SMS.ui;
  const go = role => { location.href = ui.url(ui.home(role)); };
  const loginAs = (role, id) => { SMS.login({ role, id }); go(role); };

  P.login = () => {
    const err = document.getElementById('login-error');
    document.getElementById('login-form').addEventListener('submit', e => {
      e.preventDefault();
      const email = e.target.email.value.trim().toLowerCase();
      const u = SMS.db.users.find(x => x.email.toLowerCase() === email);
      const s = SMS.db.students.find(x => x.email.toLowerCase() === email);
      if (u) return loginAs(u.role, u.id);
      if (s) { if (s.status === 'Suspended') { err.textContent = 'This account is suspended. Contact the Registry.'; err.classList.remove('hidden'); return; } return loginAs('student', s.id); }
      err.textContent = 'No account found for that email. Try a demo account below or create one.'; err.classList.remove('hidden');
    });
    document.querySelectorAll('[data-demo]').forEach(b => b.addEventListener('click', e => {
      e.preventDefault();
      const r = b.dataset.demo;
      if (r === 'student') loginAs('student', 's1');
      else if (r === 'staff') loginAs('staff', 'u1');
      else loginAs('admin', 'u9');
    }));
  };

  P.register = () => {
    const err = document.getElementById('reg-error');
    const dsel = document.getElementById('deptId');
    dsel.innerHTML = SMS.db.departments.map(d => `<option value="${d.id}">${ui.esc(d.name)}</option>`).join('');
    document.getElementById('reg-form').addEventListener('submit', e => {
      e.preventDefault(); const f = ui.formData(e.target);
      const show = m => { err.textContent = m; err.classList.remove('hidden'); };
      if (f.password.length < 8) return show('Password must be at least 8 characters.');
      if (f.password !== f.password_confirmation) return show('Passwords do not match.');
      const email = f.email.trim().toLowerCase();
      if (SMS.db.students.some(s => s.email.toLowerCase() === email) || SMS.db.users.some(u => u.email.toLowerCase() === email)) return show('An account with that email already exists.');
      const id = SMS.uid('s');
      SMS.db.students.push({ id, regNo: SMS.nextRegNo(), name: f.name.trim(), email, phone: '', gender: f.gender, dob: '', deptId: f.deptId, programme: SMS.dept(f.deptId).name, year: 1, status: 'Active', guardian: '', address: '', joined: new Date().toISOString().slice(0, 10) });
      SMS.db.fees[id] = { total: 1850000 };
      SMS.save(); loginAs('student', id);
    });
  };

  P.forgot = () => document.getElementById('forgot-form').addEventListener('submit', e => {
    e.preventDefault(); document.getElementById('forgot-form').classList.add('hidden'); document.getElementById('forgot-done').classList.remove('hidden');
  });
  P.reset = () => document.getElementById('reset-form').addEventListener('submit', e => {
    e.preventDefault(); const f = ui.formData(e.target); const err = document.getElementById('reset-error');
    if (f.password !== f.password_confirmation) { err.textContent = 'Passwords do not match.'; err.classList.remove('hidden'); return; }
    ui.toast('Password updated. Redirecting to sign in…'); setTimeout(() => location.href = 'login.html', 1200);
  });
  P.contact = () => document.getElementById('contact-form').addEventListener('submit', e => { e.preventDefault(); e.target.reset(); ui.toast('Message sent. The Registry will reply within 2 working days.'); });
  P.landing = () => {
    const d = SMS.db;
    const set = (k, v) => {
      const el = document.querySelector(`[data-stat="${k}"]`);
      if (!el) return;
      el.textContent = v;
    };

    const animateValue = (el, target) => {
      if (!el) return;
      const duration = 800;
      const start = performance.now();
      const from = Number(el.dataset.value || 0);
      const step = now => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.round(from + (target - from) * eased);
        el.textContent = value;
        if (progress < 1) requestAnimationFrame(step);
      };
      el.dataset.value = String(target);
      requestAnimationFrame(step);
    };

    const values = {
      students: d.students.length,
      courses: d.courses.length,
      departments: d.departments.length,
      lecturers: d.users.filter(u => u.role === 'staff').length,
    };

    Object.entries(values).forEach(([key, value]) => {
      const el = document.querySelector(`[data-stat="${key}"]`);
      if (!el) return;
      el.textContent = '0';
      animateValue(el, value);
    });
  };
})();
