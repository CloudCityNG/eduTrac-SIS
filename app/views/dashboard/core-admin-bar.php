<?php if (!defined('BASE_PATH')) exit('No direct script access allowed'); 
/**
 * eduTrac SIS Admin Bar.
 *
 * @license GPLv3
 * 
 * @since       6.2.0
 * @package     eduTrac SIS
 * @author      Joshua Parker <joshmac3@icloud.com>
 */
$app = \Liten\Liten::getInstance();
?>

                <!-- Top navbar -->
                <div class="navbar main">

                    <!-- Menu Toggle Button -->
                    <button type="button" class="btn btn-navbar navbar-toggle pull-left">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- // Menu Toggle Button END -->

                    <!-- Top Menu -->
                    <ul class="topnav pull-left">
                        <li><a href="<?= get_base_url(); ?>dashboard/<?= bm(); ?>" class="glyphicons dashboard"><i></i> <?= _t('Dashboard'); ?></a></li>
                        <li<?= ae('access_plugin_screen'); ?> class="dropdown dd-1">
                            <a href="" data-toggle="dropdown" class="glyphicons electrical_plug"><i></i><?= _t('Plugins'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu pull-left">
                                <li<?= ae('access_plugin_admin_page'); ?>><a href="<?= get_base_url(); ?>plugins/install/<?= bm(); ?>" class="glyphicons upload"><i></i><?= _t('Install Plugins'); ?></a></li>
                                <li<?= ae('access_plugin_screen'); ?>><a href="<?= get_base_url(); ?>plugins/<?= bm(); ?>" class="glyphicons cogwheels"><i></i><?= _t('Plugins'); ?></a></li>
                                <?php $app->hook->list_plugin_admin_pages(get_base_url() . 'plugins/options' . '/'); ?>
                                <?php 
                                /**
                                 * Use this alternative action to create admin pages
                                 * and subpages utilizing routers as well as views.
                                 * 
                                 * @since 6.1.09
                                 */
                                $app->hook->do_action('list_plugin_admin_pages');
                                ?>
                            </ul>
                        </li>
                        <li class="dropdown dd-1">
                            <a href="" data-toggle="dropdown" class="glyphicons notes"><i></i><?= _t('Screens'); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu pull-left">

                                <li<?= hl('settings', 'edit_settings'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons settings"><i></i><?= _t('Administrative'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= hl('general_settings'); ?>><a href="<?= get_base_url(); ?>setting/<?= bm(); ?>"> <?= _t('General Settings'); ?></a></li>
                                        <li<?= hl('registration_settings'); ?>><a href="<?= get_base_url(); ?>registration/<?= bm(); ?>"> <?= _t('Registration Settings'); ?></a></li>
                                        <li<?= hl('email_settings'); ?>><a href="<?= get_base_url(); ?>email/<?= bm(); ?>"> <?= _t('Email Settings'); ?></a></li>
                                        <li<?= hl('email_settings'); ?>><a href="<?= get_base_url(); ?>templates/<?= bm(); ?>"> <?= _t('Email Templates'); ?></a></li>
                                        <li<?= hl('importer', 'import_data'); ?><?= ml('import_module'); ?> class=""><a href="<?= get_base_url(); ?>form/import/<?= bm(); ?>"><?= _t('Importer'); ?></a></li>
                                        <li<?= hl('cron_jobs'); ?>><a href="<?= get_base_url(); ?>cron/<?= bm(); ?>"> <?= _t('Cronjob Handler'); ?></a></li>
                                        <li<?= hl('permissions', 'access_permission_screen'); ?>><a href="<?= get_base_url(); ?>permission/<?= bm(); ?>"> <?= _t('(MPRM) Manage Perm'); ?></a></li>
                                        <li<?= hl('roles', 'access_role_screen'); ?>><a href="<?= get_base_url(); ?>role/<?= bm(); ?>"> <?= _t('(MRLE) Manage Role'); ?></a></li>
                                        <li<?= hl('errorlogs', 'access_error_log_screen'); ?><?= ml('event_log_module'); ?>><a href="<?= get_base_url(); ?>err/logs/<?= bm(); ?>"> <?= _t('Error Log'); ?></a></li>
                                        <li<?= hl('audit_trail', 'access_audit_trail_screen'); ?><?= ml('event_log_module'); ?>><a href="<?= get_base_url(); ?>audit-trail/<?= bm(); ?>"> <?= _t('Audit Trail'); ?></a></li>
                                        <li<?= hl('automatic_update', 'edit_settings'); ?>><a href="<?= get_base_url(); ?>dashboard/core-update/<?= bm(); ?>"> <?= _t('Automatic Update'); ?></a></li>
                                    </ul>
                                </li>
                                
                                <li<?= hl('snapshot', 'access_error_log_screen'); ?>><a href="<?= get_base_url(); ?>dashboard/system-snapshot/<?= bm(); ?>" class="glyphicons camera"><i></i><?= _t('System Snapshot Report'); ?></a></li>
                                
                                <li<?= ae('access_plugin_screen'); ?> class="dropdown submenu">
                                    <a href="" data-toggle="dropdown" class="dropdown-toggle glyphicons package"><i></i><?= _t('System Modules'); ?> <span class="caret"></span></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= ae('access_plugin_screen'); ?>><a href="<?= get_base_url(); ?>dashboard/modules/<?= bm(); ?>"><?= _t('Modules'); ?></a></li>
                                        <li<?= ae('access_plugin_admin_page'); ?>><a href="<?= get_base_url(); ?>dashboard/install-module/<?= bm(); ?>"><?= _t('Install Modules'); ?></a></li>
                                    </ul>
                                </li>

                                <li<?= hl('forms', 'access_forms'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons notes_2"><i></i><?= _t('Forms'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>form/semester/<?= bm(); ?>"><?= _t('(SEM) - Semester'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/term/<?= bm(); ?>"><?= _t('(TERM) - Term'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/acad-year/<?= bm(); ?>"><?= _t('(AYR) - Academic Year'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/department/<?= bm(); ?>"><?= _t('(DEPT) - Department'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/subject/<?= bm(); ?>"><?= _t('(SUBJ) - Subject'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/student-load-rule/<?= bm(); ?>"><?= _t('(SLR) - Student Load Rules'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/degree/<?= bm(); ?>"><?= _t('(DEG) - Degree'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/major/<?= bm(); ?>"><?= _t('(MAJR) - Major'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/minor/<?= bm(); ?>"><?= _t('(MINR) - Minor'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/ccd/<?= bm(); ?>"><?= _t('(CCD) - CCD'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/specialization/<?= bm(); ?>"><?= _t('(SPEC) - Specialization'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/cip/<?= bm(); ?>"><?= _t('(CIP) - CIP'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/rstr-code/<?= bm(); ?>"><?= _t('(RSTR) - Restriction Codes'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/location/<?= bm(); ?>"><?= _t('(LOC) - Location'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/building/<?= bm(); ?>"><?= _t('(BLDG) - Building'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/room/<?= bm(); ?>"><?= _t('(ROOM) - Room'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/school/<?= bm(); ?>"><?= _t('(SCH) - School'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>form/grade-scale/<?= bm(); ?>"><?= _t('(GRSC) - Grade Scale'); ?></a></li>
                                    </ul>
                                </li>

                                <li><a href="<?= get_base_url(); ?>dashboard/support/<?= bm(); ?>" class="glyphicons life_preserver"><i></i><?= _t('Online Documentation'); ?></a></li>
                                
                                <li><a href="<?= get_base_url(); ?>staff/file-manager/<?= bm(); ?>" class="glyphicons file"><i></i><?= _t('File Manager'); ?></a></li>

                                <li<?= ml('booking_module'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons calendar"><i></i><?= _t('Events Calendar'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>calendar/<?= bm(); ?>"><?= _t('Calendar'); ?></a></li>
                                        <li<?= ae('room_request'); ?> class=""><a href="<?= get_base_url(); ?>calendar/booking-form/<?= bm(); ?>"><?= _t('Room Booking Form'); ?></a></li>
                                        <li<?= ae('room_request'); ?> class=""><a href="<?= get_base_url(); ?>calendar/events/<?= bm(); ?>"><?= _t('Manage Events'); ?></a></li>
                                        <li<?= ae('room_request'); ?> class=""><a href="<?= get_base_url(); ?>calendar/requests/<?= bm(); ?>"><?= _t('Pending Requests'); ?></a></li>
                                        <li<?= ae('edit_settings'); ?> class=""><a href="<?= get_base_url(); ?>calendar/setting/<?= bm(); ?>"><?= _t('Settings'); ?></a></li>
                                    </ul>
                                </li>

                                <li<?= hl('human_resources', 'access_human_resources'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons settings"><i></i><?= _t('Human Resources'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>hr/<?= bm(); ?>"><?= _t('Employees'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>hr/grades/<?= bm(); ?>"><?= _t('Pay Grades'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>hr/jobs/<?= bm(); ?>"><?= _t('Job Titles'); ?></a></li>
                                        <li<?= ml('timesheet_module'); ?> class=""><a href="<?= get_base_url(); ?>hr/timesheets/<?= bm(); ?>"><?= _t('Timesheets'); ?></a></li>
                                    </ul>
                                </li>

                                <li<?= hl('timesheets', 'submit_timesheets'); ?><?= ml('timesheet_module'); ?> class=""><a href="<?= get_base_url(); ?>staff/timesheets/<?= bm(); ?>" class="glyphicons stopwatch"><i></i><?= _t('Timesheets'); ?></a></li>

                                <?= $app->hook->do_action('main_nav_middle'); ?>

                                <li<?= hl('SQL', 'access_sql'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons database_plus"><i></i><?= _t('SQL'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= hl('sql_interface', 'access_sql_interface_screen'); ?>><a href="<?= get_base_url(); ?>sql/<?= bm(); ?>"><?= _t('SQL Interface'); ?></a></li>
                                        <li<?= hl('add_savequery', 'access_save_query_screens'); ?><?= ml('savedquery_module'); ?>><a href="<?= get_base_url(); ?>sql/saved-queries/add/<?= bm(); ?>"><?= _t('Create Query'); ?></a></li>
                                        <li<?= hl('savequery', 'access_save_query_screens'); ?><?= ml('savedquery_module'); ?>><a href="<?= get_base_url(); ?>sql/saved-queries/<?= bm(); ?>"><?= _t('Queries'); ?></a></li>
                                        <li<?= hl('csv_email', 'access_save_query_screens'); ?><?= ml('savedquery_module'); ?>><a href="<?= get_base_url(); ?>sql/saved-queries/csv-email/<?= bm(); ?>"><?= _t('CSV to Email Report'); ?></a></li>
                                    </ul>
                                </li>

                                <li<?= ae('access_academics'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons keynote"><i></i><?= _t('Academics'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= ae('access_acad_prog_screen'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Academic Program'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>program/<?= bm(); ?>"><?= _t('(PROG) - Program'); ?></a></li>
                                                <li<?= ae('add_acad_prog'); ?> class=""><a href="<?= get_base_url(); ?>program/add/<?= bm(); ?>"><?= _t('(APRG) - New Program'); ?></a></li>
                                                <?= $app->hook->do_action('acad_prog_nav'); ?>
                                            </ul>
                                        </li>

                                        <li<?= ae('access_course_screen'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Course'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>crse/<?= bm(); ?>"><?= _t('(CRSE) - Course'); ?></a></li>
                                                <li<?= ae('add_course'); ?> class=""><a href="<?= get_base_url(); ?>crse/add/<?= bm(); ?>"><?= _t('(ACRS) - New Course'); ?></a></li>
                                            </ul>
                                        </li>

                                        <li<?= ae('access_course_sec_screen'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Course Section'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>sect/<?= bm(); ?>"><?= _t('(SECT) - Section'); ?></a></li>
                                                <li<?= ae('register_students'); ?> class=""><a href="<?= get_base_url(); ?>sect/rgn/<?= bm(); ?>"><?= _t('(RGN) - Register'); ?></a></li>
                                                <li<?= ae('register_students'); ?><?= ml('savedquery_module'); ?>><a href="<?= get_base_url(); ?>sect/brgn/<?= bm(); ?>"><?= _t('(BRGN) - Batch Register'); ?></a></li>
                                                <li<?= ae('access_stu_roster_screen'); ?> class=""><a href="<?= get_base_url(); ?>sect/sros/<?= bm(); ?>"><?= _t('(SROS) - Student Roster'); ?></a></li>
                                                <li<?= ae('access_grading_screen'); ?><?= ml('booking_module'); ?> class=""><a href="<?= get_base_url(); ?>sect/timetable/<?= bm(); ?>"><?= _t('Timetable'); ?></a></li>
                                                <li<?= ae('access_course_sec_screen'); ?> class=""><a href="<?= get_base_url(); ?>sect/catalog/<?= bm(); ?>"><?= _t('Course Catalogs'); ?></a></li>
                                                <li<?= ae('access_grading_screen'); ?>><a href="<?= get_base_url(); ?>sect/courses/<?= bm(); ?>"><?= _t('My Course Sections'); ?></a></li>
                                            </ul>
                                        </li>

                                        <li<?= ae('register_student'); ?><?= ml('transfer_module'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Transfers'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li<?= ae('register_student'); ?> class=""><a href="<?= get_base_url(); ?>crse/extr/<?= bm(); ?>"><?= _t('(EXTR) - External Course'); ?></a></li>
                                                <li<?= ae('register_student'); ?> class=""><a href="<?= get_base_url(); ?>crse/tceq/add/<?= bm(); ?>"><?= _t('(ATCEQ) - New Transfer Eq.'); ?></a></li>
                                                <li<?= ae('register_student'); ?> class=""><a href="<?= get_base_url(); ?>crse/tceq/<?= bm(); ?>"><?= _t('(TCEQ) - Transfer Course Eq.'); ?></a></li>
                                                <li<?= ae('register_student'); ?> class=""><a href="<?= get_base_url(); ?>crse/tcre/<?= bm(); ?>"><?= _t('(TCRE) - Transfer Credit'); ?></a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li<?= ae('access_institutions_screen'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons building"><i></i><?= _t('Institution'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>appl/inst/<?= bm(); ?>"><?= _t('(INST) - Institution'); ?></a></li>
                                        <li<?= ae('add_institution'); ?> class=""><a href="<?= get_base_url(); ?>appl/inst/add<?= bm(); ?>"><?= _t('(AINST) - New Institution'); ?></a></li>
                                    </ul>
                                </li>
                                
                                <li<?= ae('access_communication_mgmt'); ?><?= ml('comm_mgmt_module'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons inbox"><i></i><?= _t('Communication Mgmt'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>cmgmt/<?= bm(); ?>"><?= _t('Email Templates'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>cmgmt/add/<?= bm(); ?>"><?= _t('Add Email Template'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>cmgmt/schedule/<?= bm(); ?>"><?= _t('Schedule Email'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>cmgmt/queue/<?= bm(); ?>"><?= _t('Email Queue'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>cmgmt/mailmerge/<?= bm(); ?>"><?= _t('Mail Merge'); ?></a></li>
                                    </ul>
                                </li>
                                <?php if(function_exists('nslc_module')) : ?>
                                <li<?= ae('access_nslc'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons hdd"><i></i><?= _t('NSLC'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/purge/<?= bm(); ?>"><?= _t('(NSCP) Purge'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/setup/<?= bm(); ?>"><?= _t('(NSCS) Setup'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/extraction/<?= bm(); ?>"><?= _t('(NSCX) Extraction'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/verification/<?= bm(); ?>"><?= _t('(NSCE) Verification'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/<?= bm(); ?>"><?= _t('(NSCC) Correction'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>nslc/file/<?= bm(); ?>"><?= _t('(NSCT) NSLC File'); ?></a></li>
                                    </ul>
                                </li>
                                <?php endif; ?>

                                <li<?= ae('access_person_mgmt'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons user"><i></i><?= _t('Person Management'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= ae('access_person_screen'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Person'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>nae/<?= bm(); ?>"><?= _t('(NAE) Name &amp; Address'); ?></a></li>
                                                <li<?= ae('add_person'); ?> class=""><a href="<?= get_base_url(); ?>nae/add/<?= bm(); ?>"><?= _t('(APER) Add Person'); ?></a></li>
                                            </ul>
                                        </li>

                                        <li<?= ae('access_staff_screen'); ?>><a href="<?= get_base_url(); ?>staff/<?= bm(); ?>"><?= _t('(STAF) Staff'); ?></a></li>

                                        <li<?= ae('access_student_screen'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Student'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li<?= ae('access_student_screen'); ?> class=""><a href="<?= get_base_url(); ?>stu/<?= bm(); ?>"><?= _t('(SPRO) Student Profile'); ?></a></li>
                                                <li<?= ae('graduate_students'); ?> class=""><a href="<?= get_base_url(); ?>stu/graduation/<?= bm(); ?>"><?= _t('Graduate Student(s)'); ?></a></li>
                                                <li<?= ae('generate_transcript'); ?> class=""><a href="<?= get_base_url(); ?>stu/tran/<?= bm(); ?>"><?= _t('(TRAN) Transcript'); ?></a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <li<?= hl('applications', 'access_application_screen'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons show_big_thumbnails"><i></i><?= _t('Application'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li class=""><a href="<?= get_base_url(); ?>appl/<?= bm(); ?>"><?= _t('(APPL) Application'); ?></a></li>
                                        <li class=""><a href="<?= get_base_url(); ?>appl/inst-attended/<?= bm(); ?>"><?= _t('Institution Attended'); ?></a></li>
                                    </ul>
                                </li>

                                <li<?= ae('access_financials'); ?><?= ml('financial_module'); ?> class="dropdown submenu">
                                    <a data-toggle="dropdown" class="dropdown-toggle glyphicons coins"><i></i><?= _t('Financials'); ?></a>
                                    <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                        <li<?= ae('access_general_ledger'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('General Ledger'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>financial/gl-accounts/<?= bm(); ?>"><?= _t('Account Chart'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/journal-entries/<?= bm(); ?>"><?= _t('Journal Entries'); ?></a></li>
                                            </ul>
                                        </li>

                                        <li<?= ae('access_student_accounts'); ?> class="dropdown submenu">
                                            <a data-toggle="dropdown" href="#" class="dropdown-toggle glyphicons chevron-right"><i></i><?= _t('Student Accounts'); ?></a>
                                            <ul class="dropdown-menu submenu-show submenu-hide pull-right">
                                                <li class=""><a href="<?= get_base_url(); ?>financial/billing-table/<?= bm(); ?>"><?= _t('Billing Tables'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/<?= bm(); ?>"><?= _t('Search Bill'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/create-bill/<?= bm(); ?>"><?= _t('Create Bill/Add Fees'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/batch/<?= bm(); ?>"><?= _t('Batch Fees'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/add-payment/<?= bm(); ?>"><?= _t('Add Payment'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/issue-refund/<?= bm(); ?>"><?= _t('Issue a Refund'); ?></a></li>
                                                <li class=""><a href="<?= get_base_url(); ?>financial/payment-plan/<?= bm(); ?>"><?= _t('Payment Plan'); ?></a></li>
                                            </ul>
                                        </li>

                                        <li<?= ae('access_payment_gateway'); ?> class=""><a href="<?= get_base_url(); ?>financial/paypal/<?= bm(); ?>"><?= _t('Paypal Gateway'); ?></a></li>
                                    </ul>
                                </li>

                        <?= $app->hook->do_action('main_nav_end'); ?>

                            </ul>
                        </li>

                        <li<?= ae('access_ea'); ?>><a href="<?= _h(get_option('edutrac_analytics_url')); ?>" class="glyphicons stats"><i></i> <?= _t('eduTrac Analytics'); ?></a></li>

                        <?= $app->hook->do_action('custom_list_menu_item'); ?>

                        <?php if (get_persondata('personType') != 'STU') { ?>
                            <li class="search open">
                                <form autocomplete="off" class="dropdown dd-1" method="post" action="<?= get_base_url(); ?>dashboard/search/">
                                    <input type="text" name="screen" placeholder="Type for suggestions . . ." data-toggle="screen" />
                                    <button type="button" class="glyphicons search"><i></i></button>
                                </form>
                            </li>
                        <?php } ?>

                        <li class="glyphs">
                            <ul>
                                <li><a href="<?= get_base_url(); ?>" class="glyphicons globe"><i></i></a></li>
                                <li<?= ae('clear_screen_cache'); ?>><a href="<?= get_base_url(); ?>dashboard/flushCache/" class="glyphicons circle_minus"><i></i></a></li>
                            </ul>
                        </li>
                    </ul>
                    <!-- // Top Menu END -->


                    <!-- Top Menu Right -->
                    <ul class="topnav pull-right hidden-xs hidden-sm">

                        <!-- Themer -->
                        <!-- <li><a href="#themer" data-toggle="collapse" class="glyphicons eyedropper single-icon"><i></i></a></li> -->
                        <!-- // Themer END -->

                        <!-- Profile / Logout menu -->
                        <li class="account dropdown dd-1">
                            <a data-toggle="dropdown" href="" class="glyphicons logout lock"><span class="hidden-tablet hidden-xs hidden-desktop-1"><?= get_persondata('uname'); ?></span><i></i></a>
                            <ul class="dropdown-menu pull-right">
                                <li class="profile">
                                    <span>
                                        <span class="heading"><?= _t('Profile'); ?> <a href="<?= get_base_url(); ?>profile/" class="pull-right"><?= _t('edit'); ?></a></span>
                                        <span class="media display-block margin-none">
                                            <span class="pull-left display-block thumb"><?= getSchoolPhoto(get_persondata('personID'), get_persondata('email'), '38'); ?></span>
                                            <a href="<?= get_base_url(); ?>profile/"><?= get_persondata('fname') . ' ' . get_persondata('lname'); ?></a><br />
                                            <?= get_persondata('email'); ?>
                                        </span>
                                        <span class="clearfix"></span>
                                    </span>
                                </li>
                                <?php if (isset($_COOKIE['SWITCH_USERBACK'])) : ?>
                                    <li>
                                        <a href="<?= get_base_url(); ?>switchUserBack/<?=$app->cookies->getSecureCookie('SWITCH_USERBACK');?>/"><?=_t('Switch Back to');?> <?=$app->cookies->getSecureCookie('SWITCH_USERNAME');?></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!isset($_COOKIE['SWITCH_USERBACK']) && !isset($_COOKIE['SCREENLOCK'])) : ?>
                                    <!-- <li><a href="<?= get_base_url(); ?>lock/" class="glyphicons lock"><?= _t('Lock Screen'); ?><i></i></a></li> -->
                                <?php endif; ?>
                                <li class="innerTB half">
                                    <span>
                                        <a class="btn btn-default btn-xs pull-right" href="<?= get_base_url(); ?>logout/"><?= _t('Sign Out'); ?></a>
                                    </span>
                                </li>
                            </ul>
                        </li>
                        <!-- // Profile / Logout menu END -->

                    </ul>
                    <div class="clearfix"></div>
                    <!-- // Top Menu Right END -->

                </div>
                <!-- Top navbar END -->