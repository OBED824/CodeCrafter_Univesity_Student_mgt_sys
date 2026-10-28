/* UI helpers + page shell (header, sidebar, footer) shared by every signed-in page. */
(function () {
  const ui = SMS.ui = {};
  const ROOT = document.body.dataset.root || '';
  ui.url = p => ROOT + p;
  ui.esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  ui.money = n => 'UGX ' + Math.round(Number(n) || 0).toLocaleString('en-US');
  ui.initials = n => String(n).replace(/^(Dr|Mr|Ms|Mrs|Prof)\.?\s+/i, '').split(/\s+/).map(w => w[0]).slice(0, 2).join('').toUpperCase();
  ui.icons = () => { if (window.lucide) window.lucide.createIcons(); };
  ui.qs = k => new URLSearchParams(location.search).get(k);
  ui.toast = msg => { const t = document.createElement('div'); t.className = 'toast'; t.textContent = msg; document.body.appendChild(t); setTimeout(() => t.remove(), 2600); };
  ui.confirm = msg => window.confirm(msg);

  const TONES = { blue: 'bg-blue-50 text-[#2563eb] border-blue-100', gold: 'bg-amber-50 text-[#d97706] border-amber-100', green: 'bg-emerald-50 text-emerald-700 border-emerald-100', red: 'bg-red-50 text-red-700 border-red-100', slate: 'bg-slate-100 text-slate-600 border-slate-200' };
  ui.badge = (t, tone) => `<span class="text-xs px-2 py-1 rounded-full border ${TONES[tone || 'slate']}">${ui.esc(t)}</span>`;
  ui.statusBadge = s => ui.badge(s, { Active: 'green', Suspended: 'red', Deferred: 'gold', Pending: 'gold', Graduated: 'blue' }[s] || 'slate');
  ui.gradeBadge = r => r ? ui.badge(r.letter + ' · ' + r.total, r.points >= 4 ? 'green' : r.points >= 2 ? 'blue' : r.points >= 1 ? 'gold' : 'red') : ui.badge('Pending', 'slate');
  ui.bar = (pct, tone) => `<div class="bar-track"><div class="bar-fill ${tone || ''}" style="width:${Math.max(0, Math.min(100, pct || 0))}%"></div></div>`;
  ui.attTone = p => p == null ? '' : p >= 75 ? 'green' : p >= 60 ? 'gold' : 'red';

  ui.heading = (title, sub, actions) => `<div class="flex flex-wrap items-start justify-between gap-3 mb-6">
    <div><h1 class="text-xl font-semibold">${title}</h1>${sub ? `<p class="text-sm text-slate-500 mt-1">${sub}</p>` : ''}</div>
    ${actions ? `<div class="flex items-center gap-2 no-print">${actions}</div>` : ''}</div>`;
  ui.btn = (label, href, icon, kind) => `<a href="${href}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium ${kind === 'ghost' ? 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100' : 'bg-[#2563eb] text-white hover:bg-[#1d4ed8]'}">${icon ? `<i data-lucide="${icon}" class="w-4 h-4 mr-2"></i>` : ''}${label}</a>`;
  ui.card = (title, body, right) => `<section class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    ${title ? `<div class="px-5 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between"><h2 class="text-sm font-semibold">${title}</h2>${right || ''}</div>` : ''}${body}</section>`;
  ui.stat = (icon, label, value, sub, tone) => `<div class="bg-white border border-slate-200 rounded-xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3"><p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">${label}</p>
    <i data-lucide="${icon}" class="w-5 h-5 ${tone === 'gold' ? 'text-[#d97706]' : 'text-[#2563eb]'}"></i></div>
    <p class="text-2xl font-bold">${value}</p>${sub ? `<p class="text-xs text-slate-500 mt-1">${sub}</p>` : ''}</div>`;
  ui.table = (heads, rows, empty) => `<div class="overflow-x-auto"><table class="w-full text-sm text-left">
    <thead class="text-xs uppercase text-slate-500 bg-slate-50 border-b border-slate-200"><tr>${heads.map(h => `<th class="px-4 py-3 font-semibold whitespace-nowrap">${h}</th>`).join('')}</tr></thead>
    <tbody class="divide-y divide-slate-100">${rows.length ? rows.join('') : `<tr><td colspan="${heads.length}" class="px-4 py-10 text-center text-slate-500">${empty || 'Nothing to show yet.'}</td></tr>`}</tbody></table></div>`;
  ui.tr = cells => `<tr class="hover:bg-slate-50">${cells.map(c => `<td class="px-4 py-3 align-middle">${c}</td>`).join('')}</tr>`;
  ui.field = (label, name, value, o) => {
    o = o || {}; const req = o.required ? ' required' : ''; const v = ui.esc(value == null ? '' : value);
    const star = o.required ? ' <span class="text-[#d97706]">*</span>' : '';
    let input;
    if (o.options) input = `<select id="${name}" name="${name}"${req} class="input-field">${o.options.map(op => { const [val, text] = Array.isArray(op) ? op : [op, op]; return `<option value="${ui.esc(val)}"${String(val) === String(value) ? ' selected' : ''}>${ui.esc(text)}</option>`; }).join('')}</select>`;
    else if (o.textarea) input = `<textarea id="${name}" name="${name}" rows="${o.rows || 4}"${req} class="input-field">${v}</textarea>`;
    else input = `<input id="${name}" name="${name}" type="${o.type || 'text'}" value="${v}"${req}${o.min != null ? ` min="${o.min}"` : ''}${o.max != null ? ` max="${o.max}"` : ''}${o.placeholder ? ` placeholder="${ui.esc(o.placeholder)}"` : ''} class="input-field">`;
    return `<div class="${o.span2 ? 'sm:col-span-2' : ''}"><label for="${name}" class="block text-sm font-medium text-slate-600 mb-2">${label}${star}</label>${input}</div>`;
  };
  ui.formData = form => Object.fromEntries(new FormData(form).entries());
  ui.formShell = (inner, cancelHref, submitLabel) => `<form id="main-form" class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm max-w-3xl">
    <div class="p-6 grid sm:grid-cols-2 gap-5">${inner}</div>
    <div class="px-4 py-4 bg-white border-t border-slate-200 flex justify-between">
      <a href="${cancelHref}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100"><i data-lucide="x" class="w-4 h-4 mr-2"></i>Cancel</a>
      <button type="submit" class="inline-flex items-center px-4 py-2 bg-[#2563eb] rounded-lg text-sm font-medium text-white hover:bg-[#1d4ed8]"><i data-lucide="save" class="w-4 h-4 mr-2"></i>${submitLabel || 'Save'}</button></div></form>`;

  // ---- navigation per role ----
  const NAV = {
    student: [['Main', [['dashboard', 'Dashboard', 'layout-dashboard', 'dashboard.html'], ['courses', 'My courses', 'book-open', 'student/courses.html'], ['results', 'Results', 'award', 'student/results.html'], ['attendance', 'Attendance', 'calendar-check', 'student/attendance.html'], ['fees', 'Fees', 'wallet', 'student/fees.html'], ['notifications', 'Notices', 'bell', 'notifications.html']]], ['Account', [['profile', 'My Account', 'user-cog', 'profile.html']]]],
    staff: [['Main', [['dashboard', 'Dashboard', 'layout-dashboard', 'staff/dashboard.html'], ['attendance', 'Attendance', 'calendar-check', 'staff/attendance.html'], ['grades', 'Grades', 'award', 'staff/grades.html'], ['notifications', 'Notices', 'bell', 'notifications.html']]], ['Account', [['profile', 'My Account', 'user-cog', 'profile.html']]]],
    admin: [['Main', [['dashboard', 'Dashboard', 'layout-dashboard', 'admin/dashboard.html']]], ['Manage', [['students', 'Students', 'users', 'admin/students/index.html'], ['courses', 'Courses', 'book-open', 'admin/courses/index.html'], ['departments', 'Departments', 'building-2', 'admin/departments/index.html'], ['fees', 'Fees', 'wallet', 'admin/fees.html'], ['notifications', 'Announcements', 'megaphone', 'notifications.html']]], ['Account', [['profile', 'My Account', 'user-cog', 'profile.html']]]],
  };
  const HOME = { student: 'dashboard.html', staff: 'staff/dashboard.html', admin: 'admin/dashboard.html' };
  const ROLE_LABEL = { student: 'Student', staff: 'Lecturer', admin: 'Administrator' };
  const ROLE_ICON = { student: 'graduation-cap', staff: 'presentation', admin: 'shield-check' };
  ui.home = role => HOME[role];

  ui.shell = function (user, active) {
    const app = document.getElementById('app');
    const nav = NAV[user.role].map(([g, items]) => `<div class="space-y-1 mb-5"><p class="px-3 text-xs font-semibold text-slate-400 uppercase mb-2">${g}</p>${items.map(([k, label, icon, href]) => {
      const on = k === active;
      return `<a href="${ui.url(href)}" class="sidebar-item border-l-3 ${on ? 'active border-[#2563eb] bg-blue-50 text-[#2563eb]' : 'border-transparent text-slate-500'} flex items-center p-2 rounded-lg hover:bg-slate-50 hover:text-slate-900 group text-sm transition-all duration-200 pl-3"><i data-lucide="${icon}" class="w-5 h-5 ${on ? 'text-[#2563eb]' : 'text-slate-400 group-hover:text-[#2563eb]'} transition-colors"></i><span class="ms-3 text-sm font-medium">${label}</span></a>`;
    }).join('')}</div>`).join('');
    const ini = ui.initials(user.name), sub = user.role === 'student' ? user.regNo : user.email;
    const latest = SMS.db.announcements.slice().sort((a, b) => b.date.localeCompare(a.date)).slice(0, 2);
    const wrap = document.createElement('div');
    wrap.innerHTML = `
    <input type="checkbox" id="sidebar-toggle" class="peer hidden">
    <label for="sidebar-toggle" class="fixed inset-0 bg-slate-900/30 z-30 hidden peer-checked:block sm:!hidden"></label>
    <header class="fixed top-0 z-50 w-full bg-white border-b border-slate-200 no-print"><div class="px-2 py-2 lg:px-5 lg:pl-3"><div class="flex items-center justify-between">
      <div class="flex items-center">
        <label for="sidebar-toggle" class="inline-flex items-center p-2 text-slate-500 rounded-lg sm:hidden hover:bg-slate-100 cursor-pointer"><span class="sr-only">Open sidebar</span><i data-lucide="menu" class="w-6 h-6"></i></label>
        <a href="${ui.url(HOME[user.role])}" class="flex items-center ms-2 md:me-8"><img src="${ui.url('assets/img/logo.jpeg')}" alt="Logo" class="h-10 w-10 me-3"><div class="flex flex-col"><span class="font-semibold text-slate-900 text-base">Student Management System</span><span class="text-xs text-slate-500">Academic records &amp; fees</span></div></a>
      </div>
      <div class="flex items-center space-x-3">
        <details class="menu-dropdown relative"><summary class="p-2 text-slate-500 rounded-lg hover:text-slate-900 hover:bg-slate-100 cursor-pointer relative list-none"><i data-lucide="bell" class="w-5 h-5"></i><span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#d97706] rounded-full"></span></summary>
          <div class="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-lg shadow-lg z-50"><div class="px-4 py-2 border-b border-slate-200 flex justify-between items-center bg-slate-50"><p class="text-sm font-medium">Announcements</p><a href="${ui.url('notifications.html')}" class="text-xs text-[#2563eb]">View all</a></div>
          ${latest.map(n => `<a href="${ui.url('notifications.html')}" class="block px-4 py-3 hover:bg-slate-50 border-b border-slate-200 last:border-0"><p class="text-sm">${ui.esc(n.title)}</p><p class="text-xs text-slate-500 mt-1">${ui.esc(n.date)}</p></a>`).join('')}</div></details>
        <details class="menu-dropdown relative"><summary class="flex items-center gap-2 bg-slate-50 rounded-lg p-1 text-sm border border-slate-200 hover:bg-slate-100 cursor-pointer list-none"><div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-[#2563eb] border border-blue-100 text-xs font-semibold">${ini}</div><div class="hidden md:block text-left mr-1"><div class="text-xs font-medium text-slate-900">${ui.esc(user.name)}</div><div class="text-[10px] text-slate-500">${ROLE_LABEL[user.role]}</div></div><i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i></summary>
          <div class="absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-lg shadow-lg z-50"><div class="px-4 py-3 border-b border-slate-200 bg-slate-50"><p class="text-sm font-medium">${ui.esc(user.name)}</p><p class="text-xs text-slate-500 truncate">${ui.esc(user.email)}</p></div>
          <a href="${ui.url('profile.html')}" class="flex items-center px-4 py-2 text-sm text-slate-600 hover:bg-slate-50"><i data-lucide="user" class="w-4 h-4 mr-2"></i>My Profile</a>
          <a href="#" id="logout" class="flex items-center px-4 py-2 text-sm text-[#d97706] hover:bg-slate-50 border-t border-slate-200"><i data-lucide="log-out" class="w-4 h-4 mr-2"></i>Sign out</a></div></details>
      </div></div></div></header>
    <aside class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 -translate-x-full peer-checked:translate-x-0 sm:translate-x-0 bg-white border-r border-slate-200 flex flex-col no-print">
      <div class="flex-1 px-3 py-4 overflow-y-auto sidebar-scrollbar">${nav}</div>
      <div class="mt-auto border-t border-slate-200 p-4"><div class="flex items-center space-x-3 mb-3"><div class="relative"><div class="w-10 h-10 rounded-full border border-slate-200 flex items-center justify-center bg-white text-sm font-semibold text-[#2563eb]">${ini}</div><div class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></div></div>
        <div class="flex-1 min-w-0"><p class="text-sm font-medium text-slate-900 truncate">${ui.esc(user.name)}</p><p class="text-xs text-slate-500 truncate">${ui.esc(sub)}</p></div></div>
        <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-slate-200"><i data-lucide="${ROLE_ICON[user.role]}" class="w-4 h-4 text-[#2563eb]"></i><span class="text-xs font-semibold text-slate-800 uppercase tracking-wide">${ROLE_LABEL[user.role]}</span></div></div>
    </aside>
    <div class="pt-16 sm:pl-64 min-h-screen flex flex-col"><div id="slot" class="flex-1 pb-8 px-4 sm:px-8 lg:px-10 pt-6"></div>
      <footer class="bg-[#0f172a] text-slate-200 mt-auto no-print"><div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14"><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10"><div><div class="flex items-center gap-3 mb-4"><div class="w-11 h-11 rounded-xl border border-slate-700 bg-slate-900 flex items-center justify-center"><i data-lucide="graduation-cap" class="w-5 h-5 text-[#60a5fa]"></i></div><div><p class="text-base font-semibold text-white">Student Management System</p><p class="text-[11px] text-slate-400">Campus operations simplified</p></div></div><p class="text-sm text-slate-400 leading-relaxed max-w-sm">A modern digital hub for records, results, attendance, fees and announcements across the campus.</p></div><div><h4 class="font-semibold text-sm uppercase tracking-[0.2em] text-slate-300 mb-4">Quick links</h4><ul class="space-y-3 text-sm text-slate-400"><li><a href="${ui.url('dashboard.html')}" class="hover:text-white">Dashboard</a></li><li><a href="${ui.url('notifications.html')}" class="hover:text-white">Notices</a></li><li><a href="${ui.url('profile.html')}" class="hover:text-white">My profile</a></li><li><a href="${ui.url('contact.html')}" class="hover:text-white">Contact us</a></li></ul></div><div><h4 class="font-semibold text-sm uppercase tracking-[0.2em] text-slate-300 mb-4">Departments</h4><ul class="space-y-3 text-sm text-slate-400"><li>Registry</li><li>Academic Affairs</li><li>Finance & Bursary</li><li>Student Services</li></ul></div><div><h4 class="font-semibold text-sm uppercase tracking-[0.2em] text-slate-300 mb-4">Support</h4><ul class="space-y-2 text-sm text-slate-400"><li><a href="mailto:support@campus.ac.ug" class="hover:text-white">support@campus.ac.ug</a></li><li>+256 414 000 000</li><li class="text-[#fbbf24] pt-1">Mon–Fri, 8:00am–5:00pm</li></ul></div></div><div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-400"><p>© 2026 <span class="font-semibold text-white">Student Management System</span>. All rights reserved.</p><p>Registry · Academics · Finance · Departments</p></div></div></footer></div>`;
    document.body.prepend(...wrap.childNodes);
    document.getElementById('slot').appendChild(app);
    document.getElementById('logout').addEventListener('click', e => { e.preventDefault(); SMS.logout(); location.href = ui.url('login.html'); });
  };

  // ---- boot: guard + render ----
  ui.boot = function (renderers) {
    const body = document.body, page = body.dataset.page, need = body.dataset.role;
    if (need) {
      const user = SMS.currentUser();
      if (!user) { location.replace(ui.url('login.html')); return; }
      if (need !== 'any' && user.role !== need) { location.replace(ui.url(HOME[user.role])); return; }
      ui.shell(user, body.dataset.nav);
      const fn = renderers[page]; if (fn) fn(document.getElementById('app'), user);
    } else if (renderers[page]) renderers[page](document.getElementById('app'));
    ui.icons();
  };
})();
