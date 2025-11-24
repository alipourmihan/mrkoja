<style>
  :root {
    --rtl-sidebar-width: 250px;
    --rtl-sidebar-mini-width: 75px;
  }

  html[dir="rtl"] body.rtl-enabled {
    direction: rtl !important;
    text-align: right !important;
  }

  html[dir="rtl"] body.rtl-enabled .logo-header {
    float: right !important;
  }

  html[dir="rtl"] body.rtl-enabled .navbar-expand-lg .navbar-nav .dropdown-menu {
    left: auto !important;
    right: 0 !important;
  }

  html[dir="rtl"] body.rtl-enabled .sidebar {
    right: 0 !important;
    left: auto !important;
    direction: rtl !important;
  }

  html[dir="rtl"] body.rtl-enabled .sidebar .nav {
    padding-right: 0 !important;
    text-align: right !important;
  }

  html[dir="rtl"] body.rtl-enabled .sidebar .nav-collapse li a .sub-item:before {
    right: -15px;
    left: auto;
  }

  html[dir="rtl"] body.rtl-enabled .main-panel {
    float: left !important;
    margin-left: 0 !important;
    margin-right: var(--rtl-sidebar-width) !important;
    width: calc(100% - var(--rtl-sidebar-width)) !important;
    direction: rtl !important;
    text-align: right !important;
  }

  html[dir="rtl"] body.rtl-enabled.sidebar_minimize .main-panel {
    margin-right: var(--rtl-sidebar-mini-width) !important;
    width: calc(100% - var(--rtl-sidebar-mini-width)) !important;
  }

  html[dir="rtl"] body.rtl-enabled .page-inner {
    padding-right: 2rem !important;
    padding-left: .75rem !important;
  }

  html[dir="rtl"] body.rtl-enabled .card,
  html[dir="rtl"] body.rtl-enabled .card-body,
  html[dir="rtl"] body.rtl-enabled .form-group,
  html[dir="rtl"] body.rtl-enabled .form-control {
    direction: rtl !important;
    text-align: right !important;
  }

  html[dir="rtl"] body.rtl-enabled .form-group label {
    text-align: right !important;
  }

  html[dir="rtl"] body.rtl-enabled .breadcrumbs {
    direction: rtl !important;
  }

  html[dir="rtl"] body.rtl-enabled .breadcrumbs li {
    float: right;
  }

  html[dir="rtl"] body.rtl-enabled .main-panel .page-header .btn-group-page-header {
    margin-left: 0 !important;
    margin-right: auto;
  }

  html[dir="rtl"] body.rtl-enabled .sidebar .user {
    text-align: right;
  }

  html[dir="rtl"] body.rtl-enabled .alert {
    direction: rtl;
    text-align: right;
  }

  html[dir="rtl"] body.rtl-enabled .sidebar_minimize .logo-header .nav-toggle {
    right: auto !important;
    left: 50%;
  }

  html[dir="rtl"] body.rtl-enabled.nav_open .main-panel,
  html[dir="rtl"] body.rtl-enabled.nav_open .main-header,
  html[dir="rtl"] body.rtl-enabled.nav_open.topbar_open .main-panel {
    transform: translate3d(-var(--rtl-sidebar-width), 0, 0) !important;
  }

  html[dir="rtl"] body.rtl-enabled.nav_open .sidebar {
    border-left: 1px solid #f1f1f1;
  }

  @media screen and (max-width: 991px) {
    html[dir="rtl"] body.rtl-enabled .sidebar {
      transform: translate3d(var(--rtl-sidebar-width), 0, 0) !important;
      margin-top: 0 !important;
    }

    html[dir="rtl"] body.rtl-enabled.nav_open .sidebar {
      transform: translate3d(0, 0, 0) !important;
    }

    html[dir="rtl"] body.rtl-enabled .main-panel {
      width: 100% !important;
      margin-right: 0 !important;
    }

    html[dir="rtl"] body.rtl-enabled.nav_open .main-panel,
    html[dir="rtl"] body.rtl-enabled.nav_open .main-header {
      transform: translate3d(-var(--rtl-sidebar-width), 0, 0) !important;
    }

    html[dir="rtl"] body.rtl-enabled .page-inner {
      padding: 1.25rem !important;
    }
  }
</style>

