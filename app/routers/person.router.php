<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
use \app\src\Core\NodeQ\etsis_NodeQ as Node;

/**
 * Person Router
 *
 * @license GPLv3
 *         
 * @since 5.0.0
 * @package eduTrac SIS
 * @author Joshua Parker <joshmac3@icloud.com>
 */
$css = [
    'css/admin/module.admin.page.form_elements.min.css',
    'css/admin/module.admin.page.tables.min.css'
];
$js = [
    'components/modules/admin/forms/elements/bootstrap-select/assets/lib/js/bootstrap-select.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-select/assets/custom/js/bootstrap-select.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/lib/js/select2.js?v=v2.1.0',
    'components/modules/admin/forms/elements/select2/assets/custom/js/select2.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/lib/js/bootstrap-datepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-datepicker/assets/custom/js/bootstrap-datepicker.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/lib/js/bootstrap-timepicker.js?v=v2.1.0',
    'components/modules/admin/forms/elements/bootstrap-timepicker/assets/custom/js/bootstrap-timepicker.init.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/js/jquery.dataTables.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/lib/extras/TableTools/media/js/TableTools.min.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/DT_bootstrap.js?v=v2.1.0',
    'components/modules/admin/tables/datatables/assets/custom/js/datatables.init.js?v=v2.1.0',
    'components/modules/admin/forms/elements/jCombo/jquery.jCombo.min.js'
];

$json_url = get_base_url() . 'api' . '/';
$flashNow = new \app\src\Core\etsis_Messages();
$email = _etsis_email();

$app->group('/nae', function () use($app, $css, $js, $json_url, $flashNow, $email) {

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/', function () {
        if (!hasPermission('access_person_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/', function () use($app, $css, $js, $json_url) {

        if ($app->req->isPost()) {
            $post = $_POST['nae'];
            $search = $app->db->person()
                ->select('person.personID,person.altID,person.fname,person.lname,person.uname,person.email')
                ->select('staff.staffID, appl.personID AS ApplicantID')
                ->_join('staff', 'person.personID = staff.staffID')
                ->_join('application', 'person.personID = appl.personID', 'appl')
                ->whereLike('CONCAT(person.fname," ",person.lname)', "%$post%")
                ->_or_()
                ->whereLike('CONCAT(person.lname," ",person.fname)', "%$post%")
                ->_or_()
                ->whereLike('CONCAT(person.lname,", ",person.fname)', "%$post%")
                ->_or_()
                ->whereLike('person.fname', "%$post%")
                ->_or_()
                ->whereLike('person.lname', "%$post%")
                ->_or_()
                ->whereLike('person.uname', "%$post%")
                ->_or_()
                ->whereLike('person.personID', "%$post%")
                ->_or_()
                ->whereLike('person.altID', "%$post%");
            $q = $search->find(function ($data) {
                $array = [];
                foreach ($data as $d) {
                    $array[] = $d;
                }
                return $array;
            });
        }

        $app->view->display('person/index', [
            'title' => 'Name and Address',
            'cssArray' => $css,
            'jsArray' => $js,
            'search' => $q
        ]);
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        if ($app->req->isPost()) {
            $nae = $app->db->person();
            foreach (_filter_input_array(INPUT_POST) as $k => $v) {
                $nae->$k = $v;
            }
            $nae->where('personID = ?', $id);

            /**
             * Fires before person record is updated.
             *
             * @since 6.1.07
             * @param object $nae Name and address object.
             */
            $app->hook->do_action('pre_update_person', $nae);

            if ($nae->update()) {
                $email = $app->db->address();
                $email->email1 = $_POST['email'];
                $email->where('personID = ?', $id)->update();

                $app->flash('success_message', $flashNow->notice(200));
                etsis_logger_activity_log_write('Update Record', 'Person (NAE)', get_name($id), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }

            /**
             *
             * @since 6.1.07
             */
            $person = get_person_by('personID', $id);
            /**
             * Fires after person record has been updated.
             *
             * @since 6.1.07
             * @param array $person
             *            Person data object.
             */
            $app->hook->do_action('post_update_person', $person);
            etsis_cache_delete($id, 'stu');
            etsis_cache_delete($id, 'person');
            redirect($app->req->server['HTTP_REFERER']);
        }

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        $appl = $app->db->application()
            ->where('personID = ?', $id)
            ->findOne();

        $addr = $app->db->address()
            ->where('addressType = "P"')->_and_()
            ->where('endDate = "0000-00-00"')->_and_()
            ->where('addressStatus = "C"')->_and_()
            ->where('personID = ?', $id);

        $q = $addr->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/view', [
                'title' => get_name($decode[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'addr' => $q,
                'staff' => $staff,
                'appl' => $appl
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/add/', function () {
        if (!hasPermission('add_person')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/add/', function () use($app, $css, $js, $json_url, $flashNow, $email) {

        $passSuffix = 'eT*';

        if ($app->req->isPost()) {
            $dob = str_replace('-', '', $_POST['dob']);
            $ssn = str_replace('-', '', $_POST['ssn']);

            if ($_POST['ssn'] > 0) {
                $password = etsis_hash_password((int) $ssn . $passSuffix);
            } elseif (!empty($_POST['dob'])) {
                $password = etsis_hash_password((int) $dob . $passSuffix);
            } else {
                $password = etsis_hash_password('myaccount' . $passSuffix);
            }

            $nae = $app->db->person();
            $nae->uname = $_POST['uname'];
            $nae->altID = $_POST['altID'];
            $nae->personType = $_POST['personType'];
            $nae->prefix = $_POST['prefix'];
            $nae->fname = $_POST['fname'];
            $nae->lname = $_POST['lname'];
            $nae->mname = $_POST['mname'];
            $nae->email = $_POST['email'];
            $nae->ssn = $_POST['ssn'];
            $nae->veteran = $_POST['veteran'];
            $nae->ethnicity = $_POST['ethnicity'];
            $nae->dob = $_POST['dob'];
            $nae->gender = $_POST['gender'];
            $nae->emergency_contact = $_POST['emergency_contact'];
            $nae->emergency_contact_phone = $_POST['emergency_contact_phone'];
            $nae->status = "A";
            $nae->approvedBy = get_persondata('personID');
            $nae->approvedDate = $app->db->NOW();
            $nae->password = $password;

            /**
             * Fires before person record is created.
             *
             * @since 6.1.07
             */
            $app->hook->do_action('pre_save_person');

            /**
             * Fires during the saving/creating of an person record.
             *
             * @since 6.1.10
             * @param array $nae
             *            Person data object.
             */
            $app->hook->do_action('save_person_db_table', $nae);

            if ($nae->save()) {
                $ID = $nae->lastInsertId();

                $role = $app->db->person_roles();
                $role->personID = $ID;
                $role->roleID = $_POST['roleID'];
                $role->addDate = $app->db->NOW();
                $role->save();

                $addr = $app->db->address();
                $addr->personID = $ID;
                $addr->address1 = $_POST['address1'];
                $addr->address2 = $_POST['address2'];
                $addr->city = $_POST['city'];
                $addr->state = $_POST['state'];
                $addr->zip = $_POST['zip'];
                $addr->country = $_POST['country'];
                $addr->addressType = "P";
                $addr->addressStatus = "C";
                $addr->startDate = $addr->NOW();
                $addr->addDate = $addr->NOW();
                $addr->addedBy = get_persondata('personID');
                $addr->phone1 = $_POST['phone'];
                $addr->email1 = $_POST['email'];

                if (isset($_POST['sendemail']) && $_POST['sendemail'] == 'send') {
                    if ($_POST['ssn'] > 0) {
                        $pass = (int) $ssn . $passSuffix;
                    } elseif (!empty($_POST['dob'])) {
                        $pass = (int) $dob . $passSuffix;
                    } else {
                        $pass = 'myaccount' . $passSuffix;
                    }

                    Node::dispense('login_details');
                    $node = Node::table('login_details');
                    $node->uname = (string) $_POST['uname'];
                    $node->email = (string) $_POST['email'];
                    $node->personid = (int) $ID;
                    $node->fname = (string) $_POST['fname'];
                    $node->lname = (string) $_POST['lname'];
                    $node->password = (string) $pass;
                    $node->altid = (string) $_POST['altID'];
                    $node->sent = (int) 0;
                    $node->save();
                }
                if ($addr->save()) {

                    /**
                     * Fires after person record has been created.
                     *
                     * @since 6.1.07
                     * @param string $pass
                     *            Plaintext password.
                     * @param array $nae
                     *            Person data object.
                     */
                    $app->hook->do_action_array('post_save_person', [
                        $pass,
                        $nae
                    ]);

                    etsis_logger_activity_log_write('New Record', 'Name and Address', get_name($ID), get_persondata('uname'));
                    $app->flash('success_message', _t('200 - Success: Ok. If checked `Send username & password to the user`, email has been sent to the queue.'));
                    redirect(get_base_url() . 'nae' . '/' . $ID . '/');
                } else {
                    $app->flash('error_message', $flashNow->notice(409));
                    redirect($app->req->server['HTTP_REFERER']);
                }
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        $app->view->display('person/add', [
            'title' => 'New Name and Address',
            'cssArray' => $css,
            'jsArray' => $js
        ]);
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/adsu/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/adsu/(\d+)/', function ($id) use($app, $css, $js, $json_url) {

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        $adsu = $app->db->person()
            ->setTableAlias('a')
            ->select('a.personID,a.fname,a.lname,a.mname')
            ->select('b.addressID,b.address1,b.address2,b.city')
            ->select('b.state,b.zip,b.addressType,b.addressStatus')
            ->_join('address', 'a.personID = b.personID', 'b')
            ->where('a.personID = ?', $id)->_and_()
            ->where('b.personID <> "NULL"');

        $q = $adsu->find(function ($data) {
            $array = [];
            foreach ($data as $d) {
                $array[] = $d;
            }
            return $array;
        });

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($q == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($q) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($q[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/adsu', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $q,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr-form/(\d+)/', function () {
        if (!hasPermission('add_address')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addr-form/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        if ($app->req->isPost()) {
            $addr = $app->db->address();
            $addr->personID = $decode[0]['personID'];
            $addr->address1 = $_POST['address1'];
            $addr->address2 = $_POST['address2'];
            $addr->city = $_POST['city'];
            $addr->state = $_POST['state'];
            $addr->zip = $_POST['zip'];
            $addr->country = $_POST['country'];
            $addr->addressType = $_POST['addressType'];
            $addr->startDate = $_POST['startDate'];
            $addr->endDate = $_POST['endDate'];
            $addr->addressStatus = $_POST['addressStatus'];
            $addr->phone1 = $_POST['phone1'];
            $addr->phone2 = $_POST['phone2'];
            $addr->ext1 = $_POST['ext1'];
            $addr->ext2 = $_POST['ext2'];
            $addr->phoneType1 = $_POST['phoneType1'];
            $addr->phoneType2 = $_POST['phoneType2'];
            $addr->email2 = $_POST['email2'];
            $addr->addDate = $addr->NOW();
            $addr->addedBy = get_persondata('personID');

            if ($addr->save()) {
                $ID = $addr->lastInsertId();
                etsis_logger_activity_log_write('New Record', 'Address', get_name($decode[0]['personID']), get_persondata('uname'));
                $app->flash('success_message', $flashNow->notice(200));
                etsis_cache_delete($id, 'stu');
                etsis_cache_delete($id, 'person');
                redirect(get_base_url() . 'nae/addr' . '/' . $ID . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/addr-form', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/addr/(\d+)/', function () {
        if (!hasPermission('access_person_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/addr/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        $json_a = _file_get_contents($json_url . 'address/addressID/' . $id . '/?key=' . _h(get_option('api_key')));
        $a_decode = json_decode($json_a, true);

        $json_p = _file_get_contents($json_url . 'person/personID/' . $a_decode[0]['personID'] . '/?key=' . _h(get_option('api_key')));
        $p_decode = json_decode($json_p, true);

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        if ($app->req->isPost()) {
            $addr = $app->db->address();
            foreach ($_POST as $k => $v) {
                $addr->$k = $v;
            }
            $addr->where('addressID = ?', $id);
            if ($addr->update()) {
                $app->flash('success_message', $flashNow->notice(200));
                etsis_logger_activity_log_write('Update Record', 'Address', get_name($a_decode[0]['personID']), get_persondata('uname'));
            } else {
                $app->flash('error_message', $flashNow->notice(409));
            }
            etsis_cache_delete($a_decode[0]['personID'], 'stu');
            etsis_cache_delete($a_decode[0]['personID'], 'person');
            redirect($app->req->server['HTTP_REFERER']);
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($a_decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($a_decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($a_decode[0]['addressID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/addr', [
                'title' => get_name($a_decode[0]['personID']),
                'cssArray' => $css,
                'jsArray' => $js,
                'addr' => $a_decode,
                'nae' => $p_decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/role/(\d+)/', function () {
        if (!hasPermission('access_user_role_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/role/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        if ($app->req->isPost()) {
            foreach ($_POST as $k => $v) {
                if (substr($k, 0, 5) == "role_") {
                    $roleID = str_replace("role_", "", $k);
                    if ($v == '0' || $v == 'x') {
                        $strSQL = sprintf("DELETE FROM `person_roles` WHERE `personID` = %u AND `roleID` = %u", $id, $roleID);
                    } else {
                        $strSQL = sprintf("REPLACE INTO `person_roles` SET `personID` = %u, `roleID` = %u, `addDate` = '%s'", $id, $roleID, $app->db->NOW());
                    }
                    $q = $app->db->query($strSQL);
                }
            }
            if ($q) {
                $app->flash('success_message', $flashNow->notice(200));
                redirect(get_base_url() . 'nae/role' . '/' . $id . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/role', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/perms/(\d+)/', function () {
        if (!hasPermission('access_user_permission_screen')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->match('GET|POST', '/perms/(\d+)/', function ($id) use($app, $css, $js, $json_url, $flashNow) {

        $json = _file_get_contents($json_url . 'person/personID/' . $id . '/?key=' . _h(get_option('api_key')));
        $decode = json_decode($json, true);

        $staff = $app->db->staff()
            ->where('staffID = ?', $id)
            ->findOne();

        if ($app->req->isPost()) {
            if (count($_POST['permission']) > 0) {
                $q = $app->db->query(sprintf("REPLACE INTO person_perms SET personID = %u, permission = '%s'", $id, maybe_serialize($_POST['permission'])));
            } else {
                $q = $app->db->query(sprintf("DELETE FROM person_perms WHERE personID = %u", $id));
            }
            if ($q) {
                $app->flash('success_message', $flashNow->notice(200));
                redirect(get_base_url() . 'nae/perms' . '/' . $id . '/');
            } else {
                $app->flash('error_message', $flashNow->notice(409));
                redirect($app->req->server['HTTP_REFERER']);
            }
        }

        /**
         * If the database table doesn't exist, then it
         * is false and a 404 should be sent.
         */
        if ($decode == false) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If the query is legit, but there
         * is no data in the table, then 404
         * will be shown.
         */ elseif (empty($decode) == true) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If data is zero, 404 not found.
         */ elseif (count($decode[0]['personID']) <= 0) {

            $app->view->display('error/404', [
                'title' => '404 Error'
            ]);
        } /**
         * If we get to this point, the all is well
         * and it is ok to process the query and print
         * the results in a html format.
         */ else {

            $app->view->display('person/perms', [
                'title' => get_name($id),
                'cssArray' => $css,
                'jsArray' => $js,
                'nae' => $decode,
                'staff' => $staff
            ]);
        }
    });

    $app->match('GET|POST', '/usernameCheck/', function () {
        $uname = get_person_by('uname', $_POST['uname']);

        if ($uname->uname == $_POST['uname']) {
            echo '1';
        }
    });

    /**
     * Before route check.
     */
    $app->before('GET|POST', '/resetPassword/(\d+)/', function () {
        if (!hasPermission('reset_person_password')) {
            redirect(get_base_url() . 'dashboard' . '/');
        }
    });

    $app->get('/resetPassword/(\d+)/', function ($id) use($app, $flashNow, $email) {

        $passSuffix = 'eT*';

        $person = get_person_by('personID', $id);

        $dob = str_replace('-', '', $person->dob);
        $ssn = str_replace('-', '', $person->ssn);
        if ($ssn > 0) {
            $pass = $ssn . $passSuffix;
        } elseif ($person->dob > '0000-00-00') {
            $pass = $dob . $passSuffix;
        } else {
            $pass = 'myaccount' . $passSuffix;
        }

        Node::dispense('reset_password');
        $node = Node::table('reset_password');
        $node->uname = (string) _h($person->uname);
        $node->email = (string) _h($person->email);
        $node->name = (string) get_name(_h($person->personID));
        $node->personid = (int) _h($person->personID);
        $node->fname = (string) _h($person->fname);
        $node->lname = (string) _h($person->lname);
        $node->password = (string) $pass;
        $node->sent = (int) 0;
        $node->save();

        $password = etsis_hash_password($pass);
        $q2 = $app->db->person();
        $q2->password = $password;
        $q2->where('personID = ?', $id);
        if ($q2->update()) {
            /**
             *
             * @since 6.1.07
             */
            $pass = [];
            $pass['pass'] = $pass;
            $pass['personID'] = $id;
            $pass['uname'] = $person->uname;
            $pass['fname'] = $person->fname;
            $pass['lname'] = $person->lname;
            $pass['email'] = $person->email;
            /**
             * Fires after successful reset of person's password.
             *
             * @since 6.1.07
             * @param array $pass
             *            Plaintext password.
             * @param string $uname
             *            Person's username
             */
            $app->hook->do_action('post_reset_password', $pass);

            etsis_desktop_notify(_t('Reset Password'), _t('Password reset; new email sent to queue.'), 'false');
            etsis_logger_activity_log_write(_t('Update Record'), _t('Reset Password'), get_name($id), get_persondata('uname'));
        } else {
            $app->flash('error_message', $flashNow->notice(409));
        }
        redirect($app->req->server['HTTP_REFERER']);
    });
});

$app->setError(function () use($app) {

    $app->view->display('error/404', [
        'title' => '404 Error'
    ]);
});
