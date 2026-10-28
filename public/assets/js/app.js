/* Boots the current page. data-page picks the renderer; data-role guards access. */
(function () {
  const P = SMS.pages, map = {
    'student-dashboard': P.studentDashboard, 'student-courses': P.studentCourses, 'student-results': P.studentResults, 'student-attendance': P.studentAttendance, 'student-fees': P.studentFees,
    'notifications': P.notifications, 'profile': P.profile,
    'staff-dashboard': P.staffDashboard, 'staff-attendance': P.staffAttendance, 'staff-grades': P.staffGrades,
    'admin-dashboard': P.adminDashboard, 'students-index': P.studentsIndex, 'students-create': P.studentsCreate, 'students-edit': P.studentsEdit, 'students-show': P.studentsShow,
    'courses-index': P.coursesIndex, 'courses-create': P.coursesCreate, 'courses-edit': P.coursesEdit, 'departments-index': P.departmentsIndex, 'departments-create': P.departmentsCreate, 'departments-edit': P.departmentsEdit, 'admin-fees': P.adminFees,
    'login': P.login, 'register': P.register, 'forgot': P.forgot, 'reset': P.reset, 'contact': P.contact, 'landing': P.landing,
  };
  SMS.ui.boot(map);
})();
