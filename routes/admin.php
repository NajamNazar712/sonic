<?php
//Admin Routes Start
Route::get('/{tiny_url}', 'ShortUrlController@get_actual_url');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    Route::prefix('activity_trail')->name('activity_trail.')->group(function () {
        Route::get('', 'Admins\ActivityTrailController@activity_trail_index')->name('index');
        Route::get('list', 'Admins\ActivityTrailController@activity_trail_list')->name('list');
    });

    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\AdminLoginController@login')->middleware('login.check')->name('login.submit');
    Route::post('/credentials', 'Auth\AdminLoginController@credentials')->name('login.credentials');
    Route::post('/verify_otp', 'Auth\AdminLoginController@verify_otp')->name('login.verify_otp');
    Route::get('access_denied', 'Admins\AdminController@access_denied')->name('access_denied');
    Route::post('save_coordinates', 'Admins\AdminController@save_coordinates')->name('save_coordinates');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('', 'Admins\AdminDashboardController@index')->name('index');
        Route::post('search', 'Admins\AdminDashboardController@statistics_search')->name('search');
        Route::get('incoming_list', 'Admins\AdminDashboardController@incoming_list')->name('incoming_list');
        Route::get('delivered_returned_list', 'Admins\AdminDashboardController@delivered_returned_list')->name('delivered_returned_list');
        Route::get('outgoing_top_customers_list', 'Admins\AdminDashboardController@outgoing_top_customers_list')->name('outgoing_top_customers_list');
        Route::get('incoming_weight_range_list', 'Admins\AdminDashboardController@incoming_weight_range_list')->name('incoming_weight_range_list');
        Route::get('outgoing_weight_range_list', 'Admins\AdminDashboardController@outgoing_weight_range_list')->name('outgoing_weight_range_list');
        Route::get('operation_forecast_search', 'Admins\AdminDashboardController@operation_forecast_search')->name('operation_forecast_search');
        Route::get('admin_profile', 'Admins\AdminDashboardController@admin_profile')->name('admin_profile');
        Route::get('edit_profile', 'Admins\AdminDashboardController@edit_profile')->name('edit_profile');
        //Search Sonic
        //        Route::get('search_sonic', 'Admins\AdminDashboardController@search_sonic')->name('search_sonic');

        //commision dashboard routes
        // Route::prefix('commission')->name('commission.')->group(function () {
        // Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('commission/User', 'Admins\AdminCommissionController@dashboard_userwise_index')->name('userwise');
        Route::post('commission/list', 'Admins\AdminCommissionController@dashboard_userwise_list')->name('list');
        Route::post('commission/data', 'Admins\AdminCommissionController@dashboard_userwise_data')->name('userwise.commission.data');
        Route::get('overall', 'Admins\AdminCommissionController@dashboard_overall_index')->name('overall');
        // });
        // });
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('index', 'Dashboard\BusinessProjectionRetentionController@dashboard')->name('index');
            Route::get('list', 'Dashboard\BusinessProjectionRetentionController@dashboard_list')->name('list');
            Route::post('update_reason', 'Dashboard\BusinessProjectionRetentionController@update_reason')->name('update_reason');
        });

        Route::get('overall/commission', 'Admins\AdminCommissionController@overall_commission_dashboard')->name('overall.commission');
        Route::post('overall/commission/list', 'Admins\AdminCommissionController@overall_commission_dashboard_list')->name('overall.commission.list');
        Route::post('overall/commission/data', 'Admins\AdminCommissionController@overall_commission_dashboard_data')->name('overall.commission.data');
    });

    Route::prefix('ftl')->name('ftl.')->group(function () {
        Route::prefix('request')->name('request.')->group(function () {
            Route::get('/', 'Admins\FTLController@ftl_request_index')->name('index');
            Route::get('/list', 'Admins\FTLController@ftl_request_list')->name('list');
            Route::get('/view/{id}', 'Admins\FTLController@ftl_request_view')->name('view');
            Route::post('/add', 'Admins\FTLController@ftl_request_add')->name('add');
            Route::prefix('comment')->name('comment.')->group(function () {
                Route::post('/add', 'Admins\FTLController@ftl_request_add_comment')->name('add');
                Route::post('/get', 'Admins\FTLController@ftl_request_get_comments')->name('get');
            });
            Route::prefix('update')->name('update.')->group(function () {
                Route::post('/shipper/{id}', 'Admins\FTLController@ftl_request_update_shipper')->name('shipper');
                Route::post('/status/{id}', 'Admins\FTLController@ftl_request_update_status')->name('status');
            });
        });
    });

    Route::prefix('operation_forecasting')->name('operation_forecasting.')->group(function () {
        Route::prefix('incoming')->name('incoming.')->group(function () {
            Route::get('{from?}/{to?}/{service_type_id?}/{hub?}/{status?}', 'Admins\AdminDashboardController@shipments_list')->name('shipments_list');
        });
        Route::prefix('outgoing')->name('outgoing.')->group(function () {
            Route::get('{from?}/{to?}/{customer_id?}', 'Admins\AdminDashboardController@outgoing_shipments_list')->name('shipments_list');
        });
    });


    Route::get('update/profile/password', 'Admins\AdminDashboardController@update_profile_password')->name('update.profile.password');
    Route::post('update/profile/password/submit', 'Admins\AdminDashboardController@update_profile_password_submit')->name('update.profile.password.submit');
    Route::post('update/profile/submit', 'Admins\AdminDashboardController@edit_profile_submit')->name('update.profile.submit');

    Route::prefix('update_one_time_profile')->name('update_one_time_profile.')->group(function () {
        Route::get('', 'Admins\AdminDashboardController@get_one_time_profile')->name('index');
        Route::get('check', 'Admins\AdminDashboardController@check_profile')->name('check');
        Route::post('submit', 'Admins\AdminDashboardController@update_one_time_profile')->name('submit');
    });



    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('', 'Admins\OrderManagementController@index')->name('index');
        Route::get('list', 'Admins\OrderManagementController@orders_list')->name('list');
        Route::post('shipment_charges', 'Admins\OrderManagementController@get_shipment_charges')->name('charges');
        Route::post('shipper_recall', 'Admins\OrderManagementController@shipper_recall')->name('shipper_recall');
        Route::get('shipment_print_status', 'Admins\OrderManagementController@shipment_print_status')->name('shipment_print_status');
        Route::post('telenor_shipments_arrival', 'Admins\OrderManagementController@telenor_shipments_arrival')->name('telenor_shipments_arrival');
        Route::post('foodpanda_shipments_arrival', 'Admins\OrderManagementController@foodpanda_shipments_arrival')->name('foodpanda_shipments_arrival');
        Route::post('shipment_cancel', 'Admins\OrderManagementController@shipment_cancel')->name('shipment_cancel');
        Route::post('shipment_cancel_reason', 'Admins\OrderManagementController@shipment_cancel_reason')->name('shipment_cancel_reason');

        Route::prefix('self_collection')->name('self_collection.')->group(function () {
            Route::get('', 'Admins\OrderManagementController@self_collection_index')->name('index');
            Route::get('list', 'Admins\OrderManagementController@self_collection_list')->name('list');
        });
    });

    Route::get('/order/pending', 'Admins\AdminDashboardController@orderPending');

    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('pending', 'Admins\AdminDashboardController@pendingAccountsList')->name('pending');
        Route::post('pending/ajax', 'Admins\AdminDashboardController@pendingAccountListAjax')->name('pending.ajax');
        Route::get('active', 'Admins\AdminDashboardController@activeAccountsList')->name('active');
        Route::post('active/ajax', 'Admins\AdminDashboardController@activeAccountListAjax')->name('active.ajax');
        Route::get('shipper_names_for_dropdown/{type}','Admins\AdminDashboardController@shipperNamesForDropdown')->name('shipper_names.dropdown');
        Route::get('shipper_names_for_dropdown_invoice/{type}','Admins\AdminDashboardController@shipperNamesForDropdown_invoice')->name('shipper_names.dropdown_invoice');
        Route::post('cancelation-days', 'Admins\AdminDashboardController@auto_cancelation_days')->name('auto_cancelation_days');
        Route::get('block', 'Admins\AdminDashboardController@blockAccountsList')->name('block');
        Route::get('block/ajax', 'Admins\AdminDashboardController@blockAccountListAjax')->name('block.ajax');
        Route::post('status/block', 'Admins\AdminDashboardController@UserStatusBlock')->name('status.block');
        Route::post('status/change', 'Admins\AdminDashboardController@UserStatusChange')->name('status.change');
        Route::put('status', 'Admins\AdminDashboardController@UserStatus')->name('status');
        Route::post('tag/submit', 'Admins\AdminDashboardController@tagSubmit')->name('tag.submit');
        Route::post('tag/submit/bulk', 'Admins\AdminDashboardController@tagSubmitBulk')->name('tag.submit.bulk');
        Route::post('set_segment/bulk', 'Admins\AdminDashboardController@setSegmentBulk')->name('set.segment_bulk');
        Route::post('reject/submit', 'Admins\AdminDashboardController@rejectReasonSubmit')->name('rejectreason.submit');
        Route::post('auto_shipment_cancel_days/submit', 'Admins\AdminShipmentCancelController@auto_shipment_cancel_days')->name('auto_shipment_cancel_days.submit');
        Route::post('kam_poc_ref_tag/submit', 'Admins\AdminDashboardController@kam_poc_ref_tag')->name('kam_poc_ref_tag.submit');
        Route::post('rate_type/submit', 'Admins\AdminCorporateAccountsController@rate_type_submit')->name('rate_type.submit');
        Route::post('/add_territory', 'Admins\AdminDashboardController@add_territory')->name('add_territory');
        Route::post('/add_segments', 'Admins\AdminDashboardController@add_segments')->name('add_segments');
        Route::get('active_today', 'Admins\AdminDashboardController@todayActiveAccountsList')->name('active.today');
        Route::post('active_today/ajax', 'Admins\AdminDashboardController@todayActiveAccountListAjax')->name('active.today.ajax');
        Route::get('kam_poc_ref_tag/info', 'Admins\AdminDashboardController@kam_poc_ref_tag_info')->name('kam_poc_ref_tag.info');
        Route::post('kam_poc_ref_tag/remove', 'Admins\AdminDashboardController@kam_poc_ref_tag_remove')->name('kam_poc_ref_tag.remove');
        Route::post('restrict_order_id/info', 'Admins\AdminDashboardController@restrict_order_id_info')->name('restrict_order_id.info');
        Route::post('restrict_order_id/submit', 'Admins\AdminDashboardController@restrict_order_id_submit')->name('restrict_order_id.submit');
        Route::post('/add_retag_territory', 'Admins\AdminDashboardController@add_retag_territory')->name('add_retag_territory');
        Route::post('add_fintech_charges', 'Admins\AdminDashboardController@add_fintech_charges')->name('add_fintech_charges');
        Route::get('user_fintech_charges', 'Admins\AdminDashboardController@user_fintech_charges')->name('user_fintech_charges');
        Route::post('add_rate_commission_corporate_reimb/{shippers}', 'Admins\AdminDashboardController@add_rate_commission_corporate_reimb')->name('add_rate_commission_corporate_reimb');
        Route::post('excluded_shippers', 'Admins\AdminDashboardController@excluded_shippers')->name('excluded_shippers');
        Route::post('faf_charges/info', 'Admins\AdminDashboardController@faf_charges_info')->name('faf_charges.info');
        Route::post('faf_charges/submit', 'Admins\AdminDashboardController@faf_charges_submit')->name('faf_charges.submit');

        Route::get('duplicate/info', 'Admins\AdminDashboardController@duplicate_info')->name('duplicate.info');
        Route::prefix('payment_cycle')->name('payment_cycle.')->group(function () {
            Route::get('info', 'Admins\AdminDashboardController@payment_cycle_info')->name('info');
            Route::post('submit', 'Admins\AdminDashboardController@payment_cycle_submit')->name('submit');
        });

        // shippper_exclude_route
        Route::post('/store_shipper_exclude', 'Admins\AdminDashboardController@shipperExclude')->name('store_shipper_exclude');

        //user profile
        Route::get('/{id}/view', 'Admins\AdminDashboardController@userProfile')->name('view.profile');
        Route::post('/updateprofile', 'Admins\AdminDashboardController@updateProfile')->name('update.profile');
        Route::get('getpickups', 'Admins\AdminDashboardController@getPickups')->name('get.pickups');
        Route::post('/updatebankinfo', 'Admins\AdminDashboardController@updateBankInfo')->name('update.bank');
        Route::post('edit/emails', 'Admins\AdminDashboardController@edit_notification_emails')->name('edit.emails');
        Route::post('add/emails', 'Admins\AdminDashboardController@add_notification_emails')->name('add.emails');
        Route::get('/{id}/documents', 'Admins\AdminDashboardController@userDocuments')->name('documents');
        Route::get('/{id}/{check}/{pdf}/documents', 'Admins\AdminDashboardController@viewUserDocuments')->name('documents.view');
        Route::get('/{id}/{approve}/{reason}/approve/documents', 'Admins\AdminDashboardController@approveDocuments')->name('documents.approve');
        Route::post('//documents/upload', 'Admins\AdminDashboardController@uploadDocuments')->name('documents.upload');
        Route::post('/documents/confirm', 'Admins\AdminDashboardController@userDocumentsConfirm')->name('documents.confirm');

        //my route
        Route::post('/documents/edit', 'Admins\AdminDashboardController@userDocumentsEdit')->name('documents.edit');

        Route::prefix('sister_account')->name('sister_account.')->group(function () {
            Route::get('{id}/add/', 'Admins\AdminDashboardController@add_sister_account_view')->name('add.account');
            Route::post('add/submit', 'Admins\AdminDashboardController@add_sister_account_submit')->name('add.submit');
            Route::get('{id}', 'Admins\AdminDashboardController@edit_sister_account_view')->name('edit.index');
            Route::post('edit/submit', 'Admins\AdminDashboardController@edit_sister_account_submit')->name('edit.submit');
            Route::post('account/info', 'Admins\AdminDashboardController@get_account_info')->name('info');
        });

        Route::prefix('substitute_account_management')->name('substitute_account_management.')->group(function () {
            Route::get('{id}', 'Admins\AdminDashboardController@substitute_accounts_view')->name('index');
            Route::get('list/{id}', 'Admins\AdminDashboardController@substitute_accounts_list')->name('list');
            Route::get('check_email/{id?}', 'Admins\AdminDashboardController@substitute_accounts_email')->name('check_email');
            Route::get('add/{id}', 'Admins\AdminDashboardController@substitute_accounts_add_index')->name('add.index');
            Route::post('add/store/{id}', 'Admins\AdminDashboardController@substitute_accounts_add_store')->name('add.store');
            Route::post('status', 'Admins\AdminDashboardController@substitute_accounts_status')->name('status');
            Route::get('update/{shipper_id}/{id}', 'Admins\AdminDashboardController@substitute_accounts_update_index')->name('update.index');
            Route::post('update/store/{shipper_id}/{id}', 'Admins\AdminDashboardController@substitute_accounts_update_store')->name('update.store');


        });

        Route::prefix('merged_account')->name('merged_account.')->group(function () {
            Route::get('', 'Admins\AdminDashboardController@merged_accounts_index')->name('index');
            Route::get('list', 'Admins\AdminDashboardController@merged_accounts_list')->name('list');
            Route::post('info', 'Admins\AdminDashboardController@merged_accounts_info')->name('info');
            Route::prefix('mapping')->name('mapping.')->group(function () {
                Route::post('mapping/info', 'Admins\AdminDashboardController@merged_accounts_mapping_info')->name('info');
                Route::post('submit', 'Admins\AdminDashboardController@merged_accounts_mapping_submit')->name('submit');
            });
        });

        Route::prefix('warehousing')->name('warehousing.')->group(function () {
            Route::post('active', 'Admins\AdminDashboardController@warehousing_active')->name('active');
            Route::post('inactive', 'Admins\AdminDashboardController@warehousing_inactive')->name('inactive');
        });

        Route::prefix('packaging')->name('packaging.')->group(function () {
            Route::post('invoice_log', 'Admins\AdminDashboardController@packaging_invoice_log')->name('invoice.log');
        });

        Route::prefix('on_delivered')->name('on_delivered.')->group(function () {
            Route::post('delivered_invoice_log', 'Admins\AdminDashboardController@delivered_invoice_log')->name('invoice.log');
        });

        Route::prefix('disable_account_intimation_survey')->name('disable.account.intimation.survey.')->group(function () {
            Route::get('', 'Admins\AdminDashboardController@disable_account_intimation_survey_index')->name('index');
            Route::get('list', 'Admins\AdminDashboardController@disable_account_intimation_survey_list')->name('list');
            Route::post('status', 'Admins\AdminDashboardController@status')->name('status');
            Route::post('add', 'Admins\AdminDashboardController@add')->name('add');
            Route::post('details', 'Admins\AdminDashboardController@details')->name('details');
            Route::post('edit', 'Admins\AdminDashboardController@edit')->name('edit');
            Route::post('send_survey', 'Admins\AdminDashboardController@send_survey')->name('send_survey');
            Route::get('report', 'Admins\AdminDashboardController@survey_report')->name('report');
            Route::get('report/list', 'Admins\AdminDashboardController@survey_report_list')->name('report.list');
            Route::get('report/submitresponse', 'Admins\AdminDashboardController@submitresponse_report')->name('report.submitresponse');
        });

        Route::prefix('kam_bulk_tagging')->name('kam_bulk_tagging.')->group(function () {
            Route::get('', 'Admins\Shippers\Accounts\KAMBulkTaggingController@index')->name('index');
            Route::post('update', 'Admins\Shippers\Accounts\KAMBulkTaggingController@update')->name('update');
        });
    });

    Route::prefix('daily_visit')->name('daily_visit.')->group(function () {
        Route::get('', 'Admins\AdminDailyVisitController@daily_visit_index')->name('index');
        Route::post('store', 'Admins\AdminDailyVisitController@daily_visit_store')->name('store');
        Route::get('business_card/{business_card}', 'Admins\AdminDailyVisitController@business_card')->name('business_card');
        Route::get('location_photo/{location_photo}', 'Admins\AdminDailyVisitController@location_photo')->name('location_photo');
        Route::prefix('screen')->name('screen.')->group(function () {
            Route::get('', 'Admins\AdminDailyVisitController@daily_visit_list_index')->name('index');
            Route::get('list', 'Admins\AdminDailyVisitController@daily_visit_list')->name('list');
            Route::get('{id}/edit', 'Admins\AdminDailyVisitController@daily_visit_edit')->name('edit');
        });
    });

    //Datatables data using ajax calls
    //add rates view
    Route::get('/accounts/{id}/add/rates', 'Admins\AdminDashboardController@addRatesView')->name('add.rates');
    Route::post('/accounts/{id}/add/rates', 'Admins\AdminDashboardController@addRates')->name('add.rates.submit');
    //edit rates
    Route::get('/accounts/{id}/edit/rates', 'Admins\AdminDashboardController@editRatesView')->name('edit.rates');
    Route::post('/edit/user_documents', 'Admins\AdminDashboardController@edit_rates_user_documents')->name('edit.user_documents');
    Route::put('/accounts/{id}/edit/rates', 'Admins\AdminDashboardController@editRates')->name('edit.rates.submit');
    Route::get('accounts/{id}/view_crf_agreement', 'ShipperAgreementController@view_crf_agreement')->name('accounts.view_crf_agreement');
    //
    Route::get('/accounts/{id}/view/rates/{date?}', 'Admins\AdminDashboardController@viewRates')->name('view.rates');
    Route::post('user_id', 'Admins\AdminDashboardController@rate_history_date')->name('view.user');
    //Route::post('/accounts/rates_history','Admins\AdminDashboardController@viewRatesHistory')->name('view.rates.history');
    Route::get('/accounts/{id}/add_contacts', 'Admins\AdminDashboardController@add_contacts')->name('accounts.add_contacts');
    Route::post('/accounts/add_contacts.store', 'Admins\AdminDashboardController@add_contacts_store')->name('accounts.add_contacts.store');



    Route::prefix('corporate')->name('corporate.')->group(function () {
        Route::get('{id}/add/rates/{rate_type_id?}', 'Admins\AdminCorporateAccountsController@add_rates_index')->name('add.rates');
        Route::post('{id}/add/rates', 'Admins\AdminCorporateAccountsController@add_rates_submit')->name('add.rates');
        Route::get('{id}/edit/rates', 'Admins\AdminCorporateAccountsController@edit_rates_index')->name('edit.rates');
        Route::post('/edit/user_documents', 'Admins\AdminDashboardController@edit_rates_user_documents')->name('edit.user_documents');
        Route::put('{id}/edit/rates', 'Admins\AdminCorporateAccountsController@edit_rates_submit')->name('edit.rates');
        Route::get('{id}/view/rates/{date?}', 'Admins\AdminCorporateAccountsController@view_rates_index')->name('view.rates');
        Route::post('reject/submit', 'Admins\AdminCorporateAccountsController@rejectReasonSubmit')->name('rejectreason.submit');
        Route::prefix('zone_wise')->name('zone_wise.')->group(function () {
            Route::post('{id}/add/rates', 'Admins\AdminCorporateAccountsController@add_rates_zone_wise_submit')->name('add.rates');
            Route::put('{id}/edit/rates', 'Admins\AdminCorporateAccountsController@edit_rates_zone_wise_submit')->name('edit.rates');
        });
        Route::prefix('default')->name('default.')->group(function () {
            Route::post('{id}/add/rates', 'Admins\AdminCorporateAccountsController@add_rates_default_submit')->name('add.rates');
            Route::get('{id}/edit/rates', 'Admins\AdminCorporateAccountsController@edit_rates_default')->name('edit.rates');
            Route::get('{id}/edit/rates/view', 'Admins\AdminCorporateAccountsController@edit_rates_default')->name('view.rates');
            Route::put('{id}/edit/rates/update', 'Admins\AdminCorporateAccountsController@edit_rates_default_submit')->name('edit.rates.submit');
            Route::post('check/corporate_rate_type', 'Admins\AdminCorporateAccountsController@check_corporate_rate_type')->name('rate_type');
            Route::post('{id}/add/rates/submit', 'Admins\AdminCorporateAccountsController@change_corporate_rate_type')->name('change_rate_type');
            Route::get('{id}/view/rates', 'Admins\AdminCorporateAccountsController@default_view_rates_index')->name('rates.view');
        });
        Route::prefix('reimbursement_setting')->name('reimbursement_setting.')->group(function () {
            Route::get('{id}', 'Admins\AdminCorporateAccountsController@corporate_reimbursement_setting')->name('index');
            Route::post('{id}', 'Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_store')->name('store');
            Route::post('{id}/approve', 'Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_approve')->name('approve');
            Route::post('{id}/reject', 'Admins\AdminCorporateAccountsController@corporate_reimbursement_setting_reject')->name('reject');
        });
    });
    //ajax request


    //new address
    Route::prefix('management')->name('management.')->group(function () {

        Route::get('/city', 'Admins\AdminDashboardController@cityView')->name('city.index');
        Route::get('/city/ajax', 'Admins\AdminDashboardController@cityListAjax')->name('city.ajax');
        Route::get('/city/form', 'Admins\AdminDashboardController@getCityForm')->name('city.form');
        Route::get('/international/city/form', 'Admins\AdminDashboardController@getInternationalCityForm')->name('international.city.form');
        Route::get('/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditCityForm')->name('city.edit');
        Route::get('/international/city/{id}/edit/form', 'Admins\AdminDashboardController@getEditInternationalCityForm')->name('international.city.edit');
        Route::post('/city', 'Admins\AdminDashboardController@addCityHub')->name('city');

        Route::post('/bulkCity', 'Admins\AdminDashboardController@addExcelCityHub')->name('addExcelCityHub')->middleware('no.cache');;

        Route::put('/city/{id}/edit/form', 'Admins\AdminDashboardController@updateCity')->name('city.edit');
        Route::put('/international/city/{id}/edit/form', 'Admins\AdminDashboardController@updateInternationalCity')->name('international.city.edit');
        Route::post('/international/city', 'Admins\AdminDashboardController@addInternationalCityHub')->name('international.city');
        Route::put('/city/status', 'Admins\AdminDashboardController@CityStatus')->name('city.status');
        Route::get('/city/{id}/status/ajax', 'Admins\AdminDashboardController@CityStatusCheck')->name('city.status.ajax');
        Route::get('', 'Admins\AdminDashboardController@walk_in_city_list')->name('city_list');
        Route::post('', 'Admins\AdminDashboardController@check_min_charges')->name('min_charges');
        Route::get('/city/{id}/add_city_sub_area', 'Admins\AdminDashboardController@add_city_sub_area')->name('add_city_sub_area');
        Route::post('/city/add_city_sub_area_ajax', 'Admins\AdminDashboardController@add_city_sub_area_ajax')->name('add_city_sub_area_ajax');
        Route::post('/city/city_sub_area_post', 'Admins\AdminDashboardController@city_sub_area_post')->name('city_sub_area_post');
        Route::post('city/city_area_status', 'Admins\AdminDashboardController@city_area_status')->name('city_area_status');
        Route::post('city/city_area_default', 'Admins\AdminDashboardController@city_area_default')->name('city_area_default');
        //        Route::post('shippingModesAjax', 'Admins\AdminDashboardController@modesAjax')->name('shippingModes.ajax');
        Route::post('city/disable_booking_status', 'Admins\AdminDashboardController@disable_booking_status')->name('disable_booking_status');
        Route::post('city/enable_booking_status', 'Admins\AdminDashboardController@enable_booking_status')->name('enable_booking_status');
        Route::get('city/status-logs/{cityId}', 'Admins\AdminDashboardController@get_city_status_logs')->name('get_city_status_logs');


        Route::get('city/{id}/changes', 'Admins\AdminDashboardController@getAjaxCityChanges')->name('getAjaxCityChanges');
        Route::get('/{id}/tagging-history', 'Admins\AdminDashboardController@taggingHistory')->name('taggingHistory');

        //Route
        Route::prefix('route')->name('route.')->group(function () {
            Route::get('/', 'Admins\AdminDashboardController@routeView')->name('index');
            Route::get('ajax', 'Admins\AdminDashboardController@routeListAjax')->name('ajax');
            Route::get('/add', 'Admins\AdminDashboardController@addRouteView')->name('add');
            Route::post('/add', 'Admins\AdminDashboardController@addRouteDetails')->name('add');
            Route::get('{id}/edit', 'Admins\AdminDashboardController@editRouteView')->name('edit');
            Route::put('{id}/edit', 'Admins\AdminDashboardController@editRouteDetails')->name('edit');
            Route::put('/status', 'Admins\AdminDashboardController@routeStatus')->name('status');
            Route::post('/assign_location', 'Admins\AdminDashboardController@assign_locations_submit')->name('assign_location');
            Route::post('/user_address', 'Admins\AdminDashboardController@user_address')->name('user_address');
            Route::post('/assign_locations_view', 'Admins\AdminDashboardController@assign_locations_view')->name('assign_locations_view');
            Route::post('/set_pickup_route', 'Admins\AdminDashboardController@set_as_pickup_route')->name('set_pickup_route');
        });


        Route::prefix('shipment_received')->name('shipment_received.')->group(function () {
            Route::get('', 'Admins\AdminDashboardController@shipment_received_details')->name('index');
            Route::get('list', 'Admins\AdminDashboardController@shipment_received_details_list')->name('list');
            Route::post('excel_upload', 'Admins\AdminDashboardController@shipment_received_excel_upload')->name('excel_upload');
        });

        Route::prefix('rider')->name('rider.')->group(function () {
            // Route::get('','Admins\AdminDashboardController@riderView')->name('index');
            Route::get('ajax', 'Admins\AdminDashboardController@riderListAjax')->name('ajax');
            Route::get('/add', 'Admins\AdminDashboardController@addRiderView')->name('add');
            Route::get('categoryAjax', 'Admins\AdminDashboardController@categoryListAjax')->name('category.ajax');
            Route::get('replacementAjax', 'Admins\AdminDashboardController@replacementListAjax')->name('replacement.ajax');
            Route::post('/add', 'Admins\AdminDashboardController@addRiderDetails')->name('add');
            Route::get('{id}/edit', 'Admins\AdminDashboardController@editRiderView')->name('edit');
            Route::put('{id}/edit', 'Admins\AdminDashboardController@editRiderDetails')->name('edit');
            Route::put('/status', 'Admins\AdminDashboardController@riderStatus')->name('status');
            Route::get('/phone_unique', 'Admins\AdminDashboardController@rider_phone_unique')->name('phone_unique');
        });
        Route::prefix('riders')->name('riders.')->group(function () {
            Route::get('/add/{type?}', 'Admins\RiderManagementController@addRiderView')->name('add');
            Route::get('categoryAjax', 'Admins\RiderManagementController@categoryListAjax')->name('category.ajax');
            Route::post('/add', 'Admins\RiderManagementController@addRiderDetails')->name('add');
            Route::post('rejoin', 'Admins\RiderManagementController@rejoin')->name('rejoin');
            Route::get('{id}/edit/{type?}', 'Admins\RiderManagementController@editRiderView')->name('edit');
            Route::put('{id}/edit', 'Admins\RiderManagementController@editRiderDetails')->name('edit');
            Route::put('/status', 'Admins\RiderManagementController@riderStatus')->name('status');
            Route::get('/phone_unique', 'Admins\RiderManagementController@rider_phone_unique')->name('phone_unique');
            Route::post('incentive', 'Admins\RiderManagementController@rider_incentive')->name('incentive');
            Route::post('permanent', 'Admins\RiderManagementController@rider_permanent')->name('permanent');
            Route::post('rider_blacklist', 'Admins\RiderManagementController@rider_blacklist')->name('rider_blacklist');
            Route::post('send_sms', 'Admins\RiderManagementController@send_sms')->name('send_sms');

            Route::prefix('permanent')->name('permanent.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@permanent_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@permanent_list')->name('list');
            });

            Route::prefix('incentive')->name('incentive.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@incentive_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@incentive_list')->name('list');
            });

            Route::prefix('blacklist')->name('blacklist.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@blacklist_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@blacklist_list')->name('list');
            });


            Route::prefix('rider_remarks')->name('rider_remarks.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@rider_remarks_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@rider_remarks_list')->name('list');
                Route::post('post', 'Admins\RiderManagementController@rider_remarks_post')->name('post');

            });


            Route::prefix('rider_request')->name('rider_request.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@rider_request_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@rider_request_list')->name('list');
                Route::post('/approve', 'Admins\RiderManagementController@approveRider')->name('approve');
            });

            Route::prefix('sms_history')->name('sms_history.')->group(function () {
                Route::get('', 'Admins\RiderManagementController@sms_history_index')->name('index');
                Route::get('list', 'Admins\RiderManagementController@sms_history_list')->name('list');
                Route::get('riders_name', 'Admins\RiderManagementController@all_riders')->name('riders_name');
            });
        });

        Route::prefix('zonal')->name('zonal.')->group(function () {
            Route::get('', 'Admins\AdminZonalManagementController@index')->name('index');
            Route::get('/list', 'Admins\AdminZonalManagementController@list')->name('list');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@add_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@add_store')->name('store');
                Route::get('/international', 'Admins\AdminZonalManagementController@add_international_index')->name('international.index');
                Route::post('/international', 'Admins\AdminZonalManagementController@add_international_store')->name('international.store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@update_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@update_store')->name('store');
            });

            Route::prefix('update/international/{id}')->name('update.international.')->group(function () {
                Route::get('', 'Admins\AdminZonalManagementController@update_international_index')->name('index');
                Route::post('', 'Admins\AdminZonalManagementController@update_international_store')->name('store');
            });

            Route::post('view_cities', 'Admins\AdminZonalManagementController@view_cities')->name('view_cities');
            Route::post('status_update', 'Admins\AdminZonalManagementController@zonal_status_update')->name('status_update');
            Route::post('duplicate_zone', 'Admins\AdminZonalManagementController@duplicate_zone')->name('duplicate_zone');
            Route::get('check_zone_name/{id?}', 'Admins\AdminZonalManagementController@check_zone_name')->name('check_zone_name');

            Route::post('update_zone_cities_gst', 'Admins\AdminZonalManagementController@update_zone_cities_gst')->name('update_zone_cities_gst');
            Route::post('add_cities','Admins\AdminZonalManagementController@add_cities')->name('add_cities');
            Route::post('search_cities','Admins\AdminZonalManagementController@search_cities')->name('search_cities');
        });

        Route::prefix('territory')->name('territory.')->group(function () {
            Route::get('', 'Admins\AdminTerritoryController@index')->name('index');
            Route::get('/list', 'Admins\AdminTerritoryController@list')->name('list');
            Route::get('/add', 'Admins\AdminTerritoryController@add')->name('add');
            Route::post('/store', 'Admins\AdminTerritoryController@store')->name('store');
            Route::post('/ajax', 'Admins\AdminTerritoryController@edit_territory_ajax')->name('edit');
            Route::post('/disable_territory', 'Admins\AdminTerritoryController@disable_territory')->name('disable_territory');
            Route::post('/enable_territory', 'Admins\AdminTerritoryController@enable_territory')->name('enable_territory');
            Route::put('{id}/update', 'Admins\AdminTerritoryController@update')->name('update');
        });
        Route::prefix('area')->name('area.')->group(function () {
            Route::get('', 'Admins\AdminTerritoryController@area_index')->name('index');
            Route::get('/list', 'Admins\AdminTerritoryController@area_list')->name('list');
            Route::get('/add', 'Admins\AdminTerritoryController@area_add')->name('add');
            Route::post('/store', 'Admins\AdminTerritoryController@area_store')->name('store');
            Route::post('/ajax', 'Admins\AdminTerritoryController@area_edit')->name('edit');
            Route::post('/disable_area_status', 'Admins\AdminTerritoryController@disable_area_status')->name('disable_area_status');
            Route::post('/enable_area_status', 'Admins\AdminTerritoryController@enable_area_status')->name('enable_area_status');
            Route::put('{id}/update', 'Admins\AdminTerritoryController@area_update')->name('update');
            Route::post('tag', 'Admins\AdminTerritoryController@area_tag')->name('tag');
        });

        Route::post('/city/osa_list', 'Admins\AdminDashboardController@osa_list')->name('city.osa_list');
    });
    Route::prefix('pickups')->name('pickups.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\AdminPickupsController@pending_assign')->name('assign');
            Route::put('multiple_cancel', 'Admins\AdminPickupsController@pending_multiple_cancel')->name('multiple_cancel');
            Route::put('cancel', 'Admins\AdminPickupsController@pending_cancel')->name('cancel');
            Route::post('bookings/all', 'Admins\AdminPickupsController@pending_all_bookings')->name('bookings.all');
            Route::post('bookings', 'Admins\AdminPickupsController@pending_bookings')->name('bookings');
        });

        Route::prefix('assigned')->name('assigned.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@assigned_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@assigned_list')->name('list');
            Route::put('cancel', 'Admins\AdminPickupsController@assigned_cancel')->name('cancel');
            Route::post('view_details', 'Admins\AdminPickupsController@assigned_view_details')->name('view_details');
            Route::post('print', 'Admins\AdminPickupsController@assigned_print')->name('print');
            Route::post('sms', 'Admins\AdminPickupsController@assigned_sms')->name('sms');
            Route::post('bookings/all', 'Admins\AdminPickupsController@assigned_all_bookings')->name('bookings.all');
            Route::post('pickups', 'Admins\AdminPickupsController@assigned_pickups')->name('pickups');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@receive_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@receive_list')->name('list');
            Route::post('pickup_note', 'Admins\AdminPickupsController@receive_pickup_note')->name('pickup_note');
            Route::post('shipment_details', 'Admins\AdminPickupsController@receive_shipment_details')->name('shipment_details');
            Route::post('shipment_remove', 'Admins\AdminPickupsController@receive_shipment_remove')->name('shipment_remove');
            Route::post('bookings/all', 'Admins\AdminPickupsController@receive_all_bookings')->name('bookings.all');

            Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                Route::post('shipment_details', 'Admins\AdminPickupsController@receive_try_and_buy_shipment_details')->name('shipment_details');
            });
            Route::prefix('arrival_of_shipments')->name('arrival_of_shipments.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_index')->name('index');
                Route::post('', 'Admins\AdminPickupsController@receive_arrival_of_shipments_store')->name('store');
            });

            Route::prefix('summary')->name('summary.')->group(function () {
                Route::get('', 'Admins\AdminPickupsController@receive_summary_index')->name('index');
                Route::get('list', 'Admins\AdminPickupsController@receive_summary_list')->name('list');
                Route::post('bookings/all', 'Admins\AdminPickupsController@summary_receive_all_bookings')->name('bookings.all');
                Route::post('received', 'Admins\AdminPickupsController@summary_receive_shipments')->name('received');

                Route::prefix('request')->name('request.')->group(function () {
                    Route::post('short_received', 'Admins\AdminPickupsController@receive_summary_request_short_received')->name('short_received');
                    Route::post('over_received', 'Admins\AdminPickupsController@receive_summary_request_over_received')->name('over_received');
                    Route::post('over_short_received', 'Admins\AdminPickupsController@receive_summary_request_over_short_received')->name('over_short_received');
                    Route::put('done', 'Admins\AdminPickupsController@receive_summary_request_done')->name('done');
                    Route::put('not_done', 'Admins\AdminPickupsController@receive_summary_request_not_done')->name('not_done');
                });
            });
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@history_index')->name('index');
            Route::get('list', 'Admins\AdminPickupsController@history_list')->name('list');
            Route::post('bookings/all', 'Admins\AdminPickupsController@history_bookings')->name('bookings.all');
        });
        Route::prefix('bookedvsreceived')->name('bookedvsreceived.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@bookedvsreceived_index')->name('index');
            Route::post('list', 'Admins\AdminPickupsController@bookedvsreceived_list')->name('list');
            Route::post('booked', 'Admins\AdminPickupsController@bookedvsreceived_booked_list')->name('booked');
            Route::post('received', 'Admins\AdminPickupsController@bookedvsreceived_received_list')->name('received');
            Route::post('delivered', 'Admins\AdminPickupsController@bookedvsreceived_delivered_list')->name('delivered');
            Route::post('returned', 'Admins\AdminPickupsController@bookedvsreceived_returned_list')->name('returned');
        });

        Route::prefix('rider')->name('rider.')->group(function () {
            Route::get('', 'Rider\RiderPickupsController@pickups_index')->name('index');
            Route::get('list', 'Rider\RiderPickupsController@pickups_list')->name('list');
            //  Route::get('v2_list', 'Rider\RiderPickupsController@pickups_list_v2')->name('v2_list');
            Route::get('shipments', 'Rider\RiderPickupsController@pickups_shipments')->name('shipments');

            Route::prefix('action_log')->name('action_log.')->group(function () {
                Route::get('', 'Rider\RiderPickupsController@pickups_action_log_index')->name('index');
                Route::get('list', 'Rider\RiderPickupsController@pickups_action_log_list')->name('list');
                // Route::get('v2_list', 'Rider\RiderPickupsController@pickups_action_log_list_v2')->name('v2_list');
            });
        });

        Route::prefix('quick_arrival_of_shipments')->name('quick_arrival_of_shipments.')->group(function () {
            Route::get('', 'Admins\AdminPickupsController@quick_arrival_of_shipments_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminPickupsController@quick_arrival_of_shipments_shipment_details')->name('shipment_details');
            Route::post('shipment_remove', 'Admins\AdminPickupsController@quick_arrival_of_shipments_remove')->name('shipment_remove');
            Route::post('', 'Admins\AdminPickupsController@quick_arrival_of_shipments_store')->name('store');
        });
    });

    Route::prefix('multiple_pieces')->name('multiple_pieces.')->group(function () {
        Route::post('upload_attachment', 'Admins\AdminShipmentPieceController@upload_attachment')->name('upload_attachment');
        Route::get('view_attachment/{id}', 'Admins\AdminShipmentPieceController@view_attachment')->name('view_attachment');
        Route::prefix('hold')->name('hold.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_index')->name('index');
            Route::get('list', 'Admins\AdminShipmentPieceController@hold_list')->name('list');
            Route::post('single_piece', 'Admins\AdminShipmentPieceController@single_piece')->name('single_piece');
            Route::post('wait_remaining_pieces', 'Admins\AdminShipmentPieceController@wait_remaining_pieces')->name('wait_remaining_pieces');
            Route::post('return_back_to_shipper', 'Admins\AdminShipmentPieceController@return_back_to_shipper')->name('return_back_to_shipper');
            Route::post('return_note_create', 'Admins\AdminShipmentPieceController@return_note_create')->name('return_note_create');
            Route::post('return_note_print', 'Admins\AdminShipmentPieceController@return_note_print')->name('return_note_print');
            Route::post('single_piece_bulk', 'Admins\AdminShipmentPieceController@single_piece_bulk')->name('single_piece_bulk');
            Route::post('wait_remaining_pieces_bulk', 'Admins\AdminShipmentPieceController@wait_remaining_pieces_bulk')->name('wait_remaining_pieces_bulk');
            Route::post('return_back_to_shipper_bulk', 'Admins\AdminShipmentPieceController@return_back_to_shipper_bulk')->name('return_back_to_shipper_bulk');
        });
        Route::prefix('add')->name('add.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_add_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminShipmentPieceController@hold_shipment_details')->name('shipment_details');
            Route::post('submit', 'Admins\AdminShipmentPieceController@hold_shipment_submit')->name('submit');
        });

        Route::prefix('resolved')->name('resolved.')->group(function () {
            Route::get('', 'Admins\AdminShipmentPieceController@hold_resolved_index')->name('index');
            Route::get('list', 'Admins\AdminShipmentPieceController@hold_resolved_list')->name('list');
        });
    });
    Route::prefix('v2_pickups')->name('v2_pickups.')->group(function () {
        Route::prefix('rider')->name('rider.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@v2_pickups_index')->name('index');
            Route::get('v2_list', 'Admins\V2Pickup\V2AdminPickupsController@pickups_list_v2')->name('list');
            Route::get('shipments', 'Admins\V2Pickup\V2AdminPickupsController@pickups_shipments')->name('shipments');
        });
        Route::prefix('action_log')->name('action_log.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pickups_action_log_index_v2')->name('index');
            Route::get('v2_list', 'Admins\V2Pickup\V2AdminPickupsController@pickups_action_log_list_v2')->name('list');
            Route::get('getCityAreas', 'Admins\V2Pickup\V2AdminPickupsController@get_city_areas')->name('get_city_areas');

        });
        Route::prefix('un_assigned')->name('un_assigned.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@unassigned_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@unassigned_list')->name('list');
        });
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pending_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@pending_list')->name('list');
            Route::put('assign', 'Admins\V2Pickup\V2AdminPickupsController@pending_assign')->name('assign');
            Route::put('update', 'Admins\V2Pickup\V2AdminPickupsController@pending_update')->name('update');
            Route::post('bookings/all', 'Admins\V2Pickup\V2AdminPickupsController@pending_all_bookings')->name('bookings.all');
            Route::post('bookings/received', 'Admins\V2Pickup\V2AdminPickupsController@pending_received_bookings')->name('bookings.received');
            Route::post('print', 'Admins\V2Pickup\V2AdminPickupsController@assigned_print')->name('print');
            Route::post('status/reminder/update', 'Admins\V2Pickup\V2AdminPickupsController@pending_reminder')->name('status.reminder.update');
            Route::post('add_remarks', 'Admins\V2Pickup\V2AdminPickupsController@add_remarks')->name('add_remarks');
            Route::post('all_remarks', 'Admins\V2Pickup\V2AdminPickupsController@all_remarks')->name('all_remarks');
            Route::post('project_arrival_print', 'Admins\V2Pickup\V2AdminPickupsController@project_arrival_print')->name('project_arrival_print');
        });
        // Receiving Sheet Rout
        Route::prefix('rider_receiving')->name('rider_receiving.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_list')->name('list');
            Route::post('check_pickup', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_check_pickup')->name('check_pickup');
            Route::post('print', 'Admins\V2Pickup\V2AdminPickupsController@rider_receiving_print')->name('print');
            Route::post('total_shipments', 'Admins\V2Pickup\V2AdminPickupsController@total_shipments')->name('total_shipments');
            Route::post('arrived_shipments', 'Admins\V2Pickup\V2AdminPickupsController@arrived_shipments')->name('arrived_shipments');
            Route::prefix('dws')->name('dws.')->group(function () {
                Route::get('', 'Admins\V2Pickup\DWSController@rider_receiving_index')->name('index');
                Route::get('/list', 'Admins\V2Pickup\DWSController@rider_receiving_list')->name('list');
                Route::post('total_shipments', 'Admins\V2Pickup\DWSController@total_dws_shipments')->name('total_shipments');
            });
        });
        // End

        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::prefix('bulk')->name('bulk.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@arrival_bulk_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_bulk_shipment_details')->name('shipment_details');
                Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                    Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_try_and_buy_shipment_details')->name('shipment_details');
                });
                Route::prefix('piece')->name('piece.')->group(function () {
                    Route::post('piece_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_details')->name('piece_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_shipment_details')->name('shipment_details');
                });
                Route::post('store', 'Admins\V2Pickup\V2AdminPickupsController@bulk_arrival_submit')->name('store');
                Route::post('weight_bypass', 'Admins\V2Pickup\V2AdminPickupsController@weight_bypass')->name('weight_bypass');

            });

            Route::prefix('individual')->name('individual.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_index')->name('index');
                Route::get('weight_scale', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_index_weight_scale')->name('index_weight_scale');

                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_shipment_details')->name('shipment_details');
                Route::post('shipment_remove', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_shipment_remove')->name('shipment_remove');
                Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                    Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_try_and_buy_shipment_details')->name('shipment_details');
                });
                Route::post('store', 'Admins\V2Pickup\V2AdminPickupsController@individual_arrival_submit')->name('store');
                Route::post('shipment_sub_segment_check', 'Admins\V2Pickup\V2AdminPickupsController@arrival_individual_shipment_sub_segment_check')->name('shipment_sub_segment_check');
            });

            Route::prefix('project_shippers')->name('project_shippers.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@project_shippers_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@project_shippers_shipment_details')->name('shipment_details');
                Route::post('shipment_remove', 'Admins\V2Pickup\V2AdminPickupsController@project_shippers_shipment_remove')->name('shipment_remove');
                Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
                    Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_try_and_buy_shipment_details')->name('shipment_details');
                });
                Route::prefix('piece')->name('piece.')->group(function () {
                    Route::post('piece_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_details')->name('piece_details');
                    Route::post('shipment_details', 'Admins\V2Pickup\V2AdminPickupsController@arrival_piece_shipment_details')->name('shipment_details');
                });
                Route::post('store', 'Admins\V2Pickup\V2AdminPickupsController@project_shippers_store')->name('store');
            });
        });

        Route::prefix('arrival_service')->name('arrival_service.')->group(function () {
            Route::prefix('service')->name('service.')->group(function () {
                Route::get('', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_index')->name('index');
                Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_details')->name('shipment_details');
                Route::post('store', 'Admins\V2Pickup\V2AdminArrivalServiceController@service_arrival_submit')->name('store');
            });
        });

        Route::prefix('pickup_route')->name('pickup_route.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminPickupsController@pickup_route_index')->name('index');
            Route::get('/list', 'Admins\V2Pickup\V2AdminPickupsController@pickup_route_list')->name('list');
            Route::post('/ajax', 'Admins\V2Pickup\V2AdminPickupsController@edit_route_ajax')->name('edit_ajax');
            //Route::post('store', 'Admins\V2Pickup\V2AdminArrivalServiceController@service_arrival_submit')->name('store');
        });

        Route::prefix('rider_tracking')->name('rider_tracking.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_index')->name('index');
            Route::get('by_rider', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_by_rider')->name('by_rider');
            Route::get('by_city', 'Admins\V2Pickup\V2RiderTrackingController@rider_tracking_by_city')->name('by_city');
        });
    });

    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\DeliveryController@pending_delivery_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@pending_list')->name('list');
        });
        Route::prefix('note')->name('note.')->group(function () {
            Route::get('', 'Admins\DeliveryController@delivery_note_index')->name('index');
            Route::post('check/rider/dncc_status', 'Admins\DeliveryController@check_rider_dncc_status')->name('rider_dncc_status');
            Route::post('shipment/info', 'Admins\DeliveryController@get_shipment_details')->name('shipment.info');
            Route::post('shipment/piece_details', 'Admins\DeliveryController@get_piece_details')->name('shipment.piece_details');
            Route::post('create', 'Admins\DeliveryController@create_delivery_note')->name('create');
            Route::post('rider_check', 'Admins\DeliveryController@delivery_note_rider_check')->name('rider_check');
            Route::post('consolidation_check', 'Admins\DeliveryController@note_consolidation_check')->name('consolidation_check');
            Route::post('operation_riders', 'Admins\DeliveryController@operation_riders')->name('operation_riders');
            Route::get('request', 'Admins\DeliveryController@request_index')->name('request_index');
            Route::get('request/list', 'Admins\DeliveryController@request_list')->name('request_list');
            Route::post('request/submit', 'Admins\DeliveryController@request_submit')->name('request_submit');
            Route::post('request/info', 'Admins\DeliveryController@delivery_note_info')->name('request.info');
            Route::post('request/approve', 'Admins\DeliveryController@request_approve')->name('request.approve');
            Route::post('otp/generate', 'Admins\DeliveryController@delivery_note_otp_generation')->name('otp.generate');
            Route::post('otp/verify', 'Admins\DeliveryController@delivery_note_otp_verification')->name('otp.verify');

            Route::get('rider_category_bypass_request', 'Admins\DeliveryController@rider_category_bypass_request')->name('rider_category_bypass_request');
            Route::get('rider_cat_request_list', 'Admins\DeliveryController@rider_cat_request_list')->name('rider_cat_request_list');
            Route::post('check_rider_cat', 'Admins\DeliveryController@check_rider_cat')->name('check_rider_cat');
            //            Route::post('check_dn_against_rider','Admins\DeliveryController@check_dn_against_rider')->name('check_dn_against_rider');
            Route::post('rider_category_submit', 'Admins\DeliveryController@rider_category_submit')->name('rider_category_submit');
            Route::post('rider_category_approve', 'Admins\DeliveryController@rider_category_approve')->name('rider_category_approve');
            Route::get('rider_category_bypass_weight', 'Admins\DeliveryController@rider_category_bypass_weight')->name('rider_category_bypass_weight');
            Route::post('weight_store', 'Admins\DeliveryController@weight_store')->name('weight_store');
        });
        Route::prefix('cash_collection')->name('cash_collection.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\DeliveryController@pending_cash_collection_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@pending_cash_collection_list')->name('list');
                //show shipemtns with fintech charges
                Route::get('show', 'Admins\DeliveryController@pending_cash_collection_showshipment')->name('showshipment');
                //end 

                Route::get('fintech_shipment', 'Admins\DeliveryController@pending_cash_collection_fintech_shipment')->name('fintechshipment');

                Route::post('collect', 'Admins\DeliveryController@pending_cash_collect')->name('collect');
                Route::post('collect_revert', 'Admins\DeliveryController@pending_cash_collect_revert')->name('collect_revert');
                Route::post('all', 'Admins\DeliveryController@pending_cash_collect_all')->name('all');
                Route::post('shipments', 'Admins\DeliveryController@cash_collection_shipments')->name('shipments');
                Route::post('onelinkpayment', 'Admins\DeliveryController@one_link_payments')->name('onelinkpayment');
                Route::post('shipments/delivered', 'Admins\DeliveryController@cash_collection_shipments_delivered')->name('shipments.delivered');
                Route::post('shipments/ccd_slip', 'Admins\DeliveryController@cash_collection_shipments_ccd_slip')->name('shipments.ccd_slip');
                Route::post('shipments/upload_ccd_receipt', 'Admins\DeliveryController@cash_collection_upload_receipt')->name('shipments.upload_ccd_receipt');
                //HBL Konnect
                Route::post('transactions/information', 'Admins\DeliveryController@hbl_konnect_transactions_information')->name('transactions.information');
                //HBL Konnect
                Route::post('snatch_collect', 'Admins\CashCollectionController@pending_cash_collect_snatch')->name('snatch_collect');

            });
            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('', 'Admins\Retail\RetailCashCollectionController@retail_index')->name('index');
                Route::get('list', 'Admins\Retail\RetailCashCollectionController@retail_list')->name('list');
                Route::post('hbl_konnect_cash', 'Admins\Retail\RetailCashCollectionController@hbl_konnect_cash')->name('hbl_konnect_cash');

                Route::prefix('pending')->name('pending.')->group(function () {
                    Route::post('shipments', 'Admins\Retail\RetailCashCollectionController@number_of_shipments')->name('shipments');
                    Route::post('shipments/delivered', 'Admins\Retail\RetailCashCollectionController@shipments_delivered')->name('shipments.delivered');
                    Route::post('collect', 'Admins\Retail\RetailCashCollectionController@pending_cash_collect')->name('collect');
                    Route::post('all', 'Admins\Retail\RetailCashCollectionController@pending_cash_collect_all')->name('collect_all');
                });
            });
        });
        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\DeliveryController@delivery_note_receive_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@receive_deliveries_list')->name('list');
            Route::post('shipments', 'Admins\DeliveryController@receive_delivery_shipments')->name('shipments');
            Route::post('shipments_verified', 'Admins\DeliveryController@receive_delivery_shipments_verified')->name('shipments_verified');
            Route::post('shipment_partial', 'Admins\DeliveryController@receive_delivery_shipment_partial')->name('shipment_partial');

            Route::post('receive_shipments_delivered', 'Admins\DeliveryController@receive_shipments_delivered')->name('receive_shipments_delivered');
            Route::post('receive_shipments_undelivered', 'Admins\DeliveryController@receive_shipments_undelivered')->name('receive_shipments_undelivered');
            Route::post('receive_shipments_pending', 'Admins\DeliveryController@receive_shipments_pending')->name('receive_shipments_pending');
            Route::get('tracking/search', 'Admins\DeliveryController@receive_delivery_search')->name('tracking.search');
            Route::get('{id}/update', 'Admins\DeliveryController@receive_delivery_update')->name('update');
            Route::get('{id}/update/list', 'Admins\DeliveryController@receive_delivery_notes_list')->name('update.list');
            Route::post('update/remove', 'Admins\DeliveryController@receive_delivery_remove')->name('update.remove');
            Route::post('update/remove_bulk', 'Admins\DeliveryController@receive_delivery_remove_bulk')->name('update.remove.bulk');
            Route::post('print', 'Admins\DeliveryController@received_print')->name('print');
            Route::get('{id}/status', 'Admins\DeliveryController@receive_delivery_status_view')->name('status');
            Route::post('password/check', 'Admins\DeliveryController@receive_delivery_password_check')->name('password.check');
            Route::post('add/status', 'Admins\DeliveryController@receive_delivery_status_submit')->name('add.status');
            Route::post('add/status/all', 'Admins\DeliveryController@receive_delivery_status_submit_all')->name('add.status.all');
            Route::get('{id}/add/list', 'Admins\DeliveryController@receive_delivery_status_list')->name('add.list');
            Route::post('reason', 'Admins\DeliveryController@receive_delivery_reason')->name('reason');
            Route::post('reason_all', 'Admins\DeliveryController@receive_delivery_reason_all')->name('reason_all');
            Route::post('delivered', 'Admins\DeliveryController@receive_delivery_status_delivered')->name('delivered');
            Route::post('shipmentstatuscheck', 'Admins\DeliveryController@receive_delivery_status_check')->name('shipmentstatuscheck');
            Route::post('replacements', 'Admins\DeliveryController@receive_delivery_get_replacements')->name('replacements');
            Route::put('replacements/submit', 'Admins\DeliveryController@receive_delivery_replacements_submit')->name('replacements.submit');
            Route::post('trybuys', 'Admins\DeliveryController@receive_delivery_get_trybuys')->name('trybuys');
            Route::put('trybuys.submit', 'Admins\DeliveryController@receive_delivery_trybuys_submit')->name('trybuys.submit');
            //Non Service Area Routes
            Route::post('nsa_shipments_data', 'Admins\DeliveryController@nsa_shipments_data')->name('nsa_shipments_data');
            Route::put('nsa_shipments/submit', 'Admins\DeliveryController@nsa_shipments_submit')->name('nsa_shipments.submit');
            //Non Service Area Routes

            Route::post('distribution', 'Admins\DeliveryController@receive_delivery_get_distribution')->name('distribution');
            Route::put('distribution/submit', 'Admins\DeliveryController@receive_delivery_distribution_submit')->name('distribution.submit');


            Route::get('{id}/status/verify', 'Admins\DeliveryController@receive_delivery_note_verify_view')->name('status.verify');
            Route::get('{id}/verify/status/list', 'Admins\DeliveryController@receive_delivery_verify_status_list')->name('verify.status.list');
            Route::put('verify/status/submit', 'Admins\DeliveryController@receive_delivery_verify_status_submit')->name('verify.status.submit');
            Route::post('dncc/print', 'Admins\DeliveryController@dncc_print')->name('dncc.print');
            Route::post('undelivered/print', 'Admins\DeliveryController@dncc_undelivered_print')->name('undelivered.print');
            Route::post('reassign_rider', 'Admins\DeliveryController@reassign_rider')->name('reassign_rider');
            Route::post('/add/tracking_number', 'Admins\DeliveryController@add_shipments_in_receive_deliveries')->name('add.shipments');
            Route::post('/upload_pod', 'Admins\DeliveryController@upload_pod')->name('upload_pod');

            Route::post('fake_status_shipments', 'Admins\DeliveryController@fake_status_shipments')->name('fake_status_shipments');


            Route::get('/replacement_weight_check', 'Admins\DeliveryController@replacement_weight_check')->name('replacement_weight_check');


        });
        Route::prefix('completed')->name('completed.')->group(function () {
            Route::get('', 'Admins\DeliveryController@completed_deliveries_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@completed_receive_deliveries_list')->name('list');
            Route::post('deposit/dncc', 'Admins\DeliveryController@completed_deliveries_selected_dncc')->name('deposit.dncc');
            Route::get('sdn/create', 'Admins\DeliveryController@create_sdn_view')->name('sdn.create');
            Route::post('sdn/create', 'Admins\DeliveryController@create_sdn_submit')->name('sdn.create.submit');
            Route::get('dncc/list', 'Admins\DeliveryController@get_sdn_list')->name('dncc.list');
            Route::post('shipments', 'Admins\DeliveryController@completed_shipments')->name('shipments');
            Route::post('shipments/delivered', 'Admins\DeliveryController@completed_shipments_delivered')->name('shipments.delivered');


            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('', 'Admins\Retail\RetailCompletedDeliveries@index')->name('index');
                Route::get('list', 'Admins\Retail\RetailCompletedDeliveries@list')->name('list');
                Route::post('shipments/delivered', 'Admins\Retail\RetailCompletedDeliveries@shipments_delivered')->name('shipments.delivered');

                Route::post('deposit/pncc', 'Admins\Retail\RetailCompletedDeliveries@completed_deliveries_selected_pncc')->name('deposit.pncc');
                Route::get('pncc/list', 'Admins\Retail\RetailCompletedDeliveries@get_sdn_list')->name('pncc.list');

                Route::get('sdn/create', 'Admins\Retail\RetailCompletedDeliveries@create_sdn_view')->name('sdn.create');
                Route::post('sdn/create', 'Admins\Retail\RetailCompletedDeliveries@create_sdn_submit')->name('sdn.create.submit');
            });
        });
        Route::prefix('sdn')->name('sdn.')->group(function () {
            Route::get('', 'Admins\DeliveryController@sdn_view')->name('index');
            Route::get('list', 'Admins\DeliveryController@sdn_list')->name('list');
            Route::post('get/petty_cash_statements', 'Admins\DeliveryController@get_petty_cash_statements')->name('get.petty_cash_statements');
            Route::post('get/adjustment_reference', 'Admins\DeliveryController@get_adjustment_reference')->name('get.adjustment_reference');
            Route::post('back_to_deposit', 'Admins\DeliveryController@back_to_deposit')->name('back_to_deposit');
            Route::post('closed', 'Admins\DeliveryController@closed')->name('closed');
            Route::post('resolved', 'Admins\DeliveryController@resolved')->name('resolved');
            Route::post('bulk_closed', 'Admins\DeliveryController@bulk_closed')->name('bulk_closed');
            Route::post('bulk_resolved', 'Admins\DeliveryController@bulk_resolved')->name('bulk_resolved');
            Route::post('dn', 'Admins\DeliveryController@sdn_dncc_list')->name('dn');
            Route::get('{id}/details', 'Admins\DeliveryController@sdn_details')->name('details');
            Route::get('{id}/ajax', 'Admins\DeliveryController@sdn_details_ajax')->name('ajax');
            Route::post('slip', 'Admins\DeliveryController@sdn_deposit_slip')->name('slip');
            Route::post('print', 'Admins\DeliveryController@sdn_deposit_slip_print')->name('print');
            Route::post('dncc/print', 'Admins\DeliveryController@sdn_dncc_print')->name('dncc.print');
            Route::post('shipments', 'Admins\DeliveryController@sdn_delivered_shipments')->name('shipments');
            Route::post('slip_view', 'Admins\DeliveryController@sdn_slip_view')->name('slip_view');
            Route::post('adjustment/add', 'Admins\DeliveryController@sdn_adjustment_add')->name('adjustment.add');
            Route::get('petty_cash_detail', 'Admins\DeliveryController@sdn_petty_cash_detail')->name('petty_cash_detail');

            Route::post('status_logs', 'Admins\DeliveryController@sdn_status_logs')->name('status_logs');

            Route::post('sdn_actions', 'Admins\DeliveryController@sdn_actions')->name('sdn_actions');

            Route::post('sdn_deposit_slip_logs', 'Admins\DeliveryController@sdn_deposit_slip_logs')->name('sdn_deposit_slip_logs');

            Route::prefix('retail')->name('retail.')->group(function () {
                Route::get('{id}/details', 'Admins\Retail\RetailCompletedDeliveries@sdn_details')->name('details');
                Route::get('{id}/ajax', 'Admins\Retail\RetailCompletedDeliveries@sdn_details_ajax')->name('ajax');
            });

            Route::prefix('dncc')->name('dncc.')->group(function () {
                Route::post('add', 'Admins\DeliveryController@add_dncc')->name('add');
                Route::post('get/dncc', 'Admins\DeliveryController@get_dncc_to_add')->name('get.add');
                Route::post('remove', 'Admins\DeliveryController@remove_dncc')->name('remove');
                Route::post('get/dncc/remove', 'Admins\DeliveryController@get_dncc_to_remove')->name('get.remove');
            });

            Route::prefix('pncc')->name('pncc.')->group(function () {
                Route::post('add', 'Admins\DeliveryController@add_pncc')->name('add');
                Route::post('get/pncc', 'Admins\DeliveryController@get_pncc_to_add')->name('get.add');
                Route::post('remove', 'Admins\DeliveryController@remove_pncc')->name('remove');
                Route::post('get/pncc/remove', 'Admins\DeliveryController@get_pncc_to_remove')->name('get.remove');
            });
        });
        Route::prefix('misroute')->name('misroute.')->group(function () {
            Route::get('', 'Admins\DeliveryController@misroute_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@misroute_list')->name('list');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\MisroutedHistoryController@misrouted_history_index')->name('index');
                Route::get('list', 'Admins\MisroutedHistoryController@misrouted_history_list')->name('list');
            });
            Route::prefix('update')->name('update.')->group(function () {
                Route::get('', 'Admins\DeliveryController@misrouted_update_index')->name('index');
                //                Route::get('list','Admins\DeliveryController@misrouted_update_list')->name('list');
                Route::post('shipment/info', 'Admins\DeliveryController@get_misroute_shipment_info')->name('shipment.info');
                Route::post('store', 'Admins\DeliveryController@misroute_shipment_update')->name('store');
                Route::get('excel', 'Admins\DeliveryController@misroute_shipment_excel')->name('excel');
            });
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\DeliveryController@history_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@history_list')->name('list');
            Route::post('shipments', 'Admins\DeliveryController@history_shipments')->name('shipments');
            Route::post('shipments/delivered', 'Admins\DeliveryController@history_shipments_delivered')->name('shipments.delivered');
        });

        Route::prefix('shipments')->name('delivery_shipments.')->group(function () {
            Route::get('', 'Admins\DeliveryController@shipment_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@shipment_list')->name('list');
            Route::get('notes', 'Admins\DeliveryController@delivery_notes_list')->name('notes');

        });

        Route::prefix('quick_receiving')->name('quick_receiving.')->group(function () {
            Route::get('', 'Admins\DeliveryController@quick_receiving_delivery_index')->name('index');
            Route::post('', 'Admins\DeliveryController@quick_receiving_submit')->name('submit');
            Route::post('track_delivery_note', 'Admins\DeliveryController@quick_receiving_track_delivery_note')->name('track_delivery_note');
            Route::post('track_tracking_number', 'Admins\DeliveryController@quick_receiving_track_tracking_number')->name('track_tracking_number');
        });

        Route::prefix('signature')->name('signature.')->group(function () {
            Route::get('', 'Admins\DeliveryController@signature_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@signature_list')->name('list');
        });

        //Lost Module Start
        Route::prefix('lost')->name('lost.')->group(function () {
            Route::get('', 'Admins\LostShipmentsController@lost_shipments_index')->name('index');
            Route::post('list', 'Admins\LostShipmentsController@lost_shipments_list')->name('list');
            Route::get('lost_responsible_list', 'Admins\LostShipmentsController@lost_responsible_list')->name('lost_responsible_list');
            // for lost shipment screen only
            Route::get('lost_shipment_responsible_list', 'Admins\LostShipmentsController@lost_shipment_responsible_list')->name('lost_shipment_responsible_list');
            Route::get('old_lost_shipment_responsible_list', 'Admins\LostShipmentsController@old_lost_shipment_responsible_list')->name('old_lost_shipment_responsible_list');

            Route::post('confirm/status', 'Admins\LostShipmentsController@shipment_confirm_status')->name('confirm.status.lost');
            Route::post('reattempt/status', 'Admins\LostShipmentsController@shipment_reattempt_status')->name('reattempt.status.lost');
            Route::post('approve/status', 'Admins\LostShipmentsController@shipment_approve_status')->name('approve.status.lost');
            Route::post('lost_data', 'Admins\LostShipmentsController@lost_data')->name('lost_data');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('index', 'Admins\LostShipmentsController@lost_add_index')->name('index');
                Route::post('shipment/info', 'Admins\LostShipmentsController@get_shipment_info')->name('shipment.info');
                Route::post('shipment/store', 'Admins\LostShipmentsController@add_lost_shipments')->name('shipments.store');
                Route::post('bulk/lost/shipments', 'Admins\LostShipmentsController@bulk_lost_shipments')->name('bulk.lost');
            });
        });
        //Lost Module End

        Route::prefix('intercept')->name('intercept.')->group(function () {
            Route::get('', 'Admins\DeliveryController@intercept_request_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@intercept_request_list')->name('list');
            Route::post('approve', 'Admins\DeliveryController@approve')->name('approve');
            Route::post('reject', 'Admins\DeliveryController@reject')->name('reject');
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_request_history_index')->name('index');
                Route::get('list', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_request_history_list')->name('list');
            });
        });

        Route::prefix('fake_status')->name('fake_status.')->group(function () {
            Route::get('', 'Admins\DeliveryController@fake_status_remove_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@fake_status_remove_list')->name('list');
            Route::post('remove', 'Admins\DeliveryController@fake_status_remove')->name('remove');
            Route::prefix('log')->name('log.')->group(function () {
                Route::get('', 'Admins\DeliveryController@log_fake_statuses_index')->name('index');
                Route::post('store', 'Admins\DeliveryController@log_fake_statuses_store')->name('store');
            });
        });
        Route::prefix('replacement')->name('replacement.')->group(function () {
            Route::prefix('not_collected')->name('not_collected.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_not_collected_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@replacement_not_collected_list')->name('list');
                Route::post('re_attempt', 'Admins\DeliveryController@replacement_not_collected_re_attempt')->name('re_attempt');
                Route::post('regular_re_attempt', 'Admins\DeliveryController@replacement_not_collected_regular_re_attempt')->name('regular_re_attempt');
            });
            Route::prefix('collected')->name('collected.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_collected_index')->name('index');
                Route::post('list', 'Admins\DeliveryController@change_shipment_booking_type_shipment_details')->name('shipment_details');
                Route::post('change_booking_type', 'Admins\DeliveryController@change_shipment_booking_type')->name('change_booking_type');
            });
            Route::prefix('logs')->name('logs.')->group(function () {
                Route::get('', 'Admins\DeliveryController@replacement_to_regular_logs_index')->name('index');
                Route::get('list', 'Admins\DeliveryController@replacement_to_regular_logs_list')->name('list');
            });
        });

        Route::prefix('rider_request')->name('rider_request.')->group(function () {
            Route::get('', 'Admins\DeliveryController@rider_request_note_index')->name('index');
            Route::get('list', 'Admins\DeliveryController@rider_request_note_list')->name('list');
            Route::post('shipments', 'Admins\DeliveryController@request_note_shipments')->name('shipments');
            Route::get('{id}/approve', 'Admins\DeliveryController@request_note_approve')->name('approve');
            Route::get('{id}/reject', 'Admins\DeliveryController@request_note_reject')->name('reject');
            Route::get('{id}/update', 'Admins\DeliveryController@request_note_update')->name('update');
            Route::get('{id}/update/list', 'Admins\DeliveryController@request_note_update_list')->name('update.list');
            Route::post('update/remove', 'Admins\DeliveryController@request_note_remove')->name('update.remove');
            Route::post('update/remove_bulk', 'Admins\DeliveryController@request_note_remove_bulk')->name('update.remove.bulk');
            Route::post('/add/tracking_number', 'Admins\DeliveryController@add_shipments_in_request_note')->name('add.shipments');
        });
    });
    Route::prefix('return')->name('return.')->group(function () {
        Route::get('', 'Admins\ReturnController@return_view')->name('index');
        Route::post('list', 'Admins\ReturnController@return_marked_list')->name('list');
        Route::get('dashboard', 'Admins\ReturnController@dashboard')->name('dashboard');
        Route::get('data', 'Admins\ReturnController@return_view_data')->name('data');
        Route::post('confirm/status', 'Admins\ReturnController@return_confirm_status')->name('confirm.status');
        Route::post('reattempt/status', 'Admins\ReturnController@return_reattempt_status')->name('reattempt.status');
        Route::post('update_call_status', 'Admins\ReturnController@update_call_status')->name('update_call_status');
        Route::post('call_status_history', 'Admins\ReturnController@call_status_history')->name('call_status_history');

        Route::post('marked/status/single', 'Admins\ReturnController@return_marked_single_status')->name('marked.status.single');
        Route::get('confirmed', 'Admins\ReturnController@return_confirmed_view')->name('confirmed');
        Route::get('confirmed/list', 'Admins\ReturnController@return_confirmed_list')->name('confirmed.list');
        Route::post('confirmed/search', 'Admins\ReturnController@return_confirmed_search')->name('confirmed.search');
        Route::post('excel/store', 'Admins\ReturnController@excel_store')->name('excel.store');
        Route::post('excel/assign_agent_excel', 'Admins\ReturnController@assign_agent_excel')->name('excel.assign_agent_excel');
        Route::get('fetch/agent', 'Admins\ReturnController@fetch_agent')->name('fetch.agent');
        Route::post('assign/agent', 'Admins\ReturnController@assign_agent')->name('assign.agent');
        Route::post('unassign/agent', 'Admins\ReturnController@unassign_agent')->name('unassign.agent');
        Route::get('/confirmation_pending/sms', 'Admins\ReturnController@confirmation_pending_sms_index')->name('confirmation_pending_sms');
        Route::get('/confirmation_pending/sms/list', 'Admins\ReturnController@confirmation_pending_sms_list')->name('confirmation_pending_sms_list');
        Route::get('/confirmation_pending/manual/sms', 'Admins\ReturnController@confirmation_pending_manual_sms_index')->name('confirmation_pending_manual_sms');
        Route::get('/confirmation_pending/manual/sms/list', 'Admins\ReturnController@confirmation_pending_manual_sms_list')->name('confirmation_pending_manual_sms_list');

        Route::post('marked/self_collection', 'Admins\ReturnController@change_status_to_self_collection')->name('marked.self_collection');
        Route::post('edit/estimated_charges', 'Admins\ReturnController@update_estimated_charges')->name('edit.estimated_charges');
        Route::post('rcp_sms', 'Admins\ReturnController@manual_rcp_sms')->name('rcp_sms');

        Route::prefix('confirmed')->name('confirmed.')->group(function () {
            Route::post('revert', 'Admins\ReturnController@return_confirmed_revert')->name('revert');
            Route::post('revert/status', 'Admins\ReturnController@return_revert_status')->name('revert.status');
            Route::post('excel/store', 'Admins\ReturnController@excel_store_revert')->name('excel.store');
        });

        Route::prefix('create')->name('create.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_create_index')->name('index');
            Route::get('riders/by_hub', 'Admins\ReturnController@get_riders_by_hub')->name('riders.hub');
            Route::post('shipment_details', 'Admins\ReturnController@get_shipment_details')->name('shipment_details');
            Route::post('shipment/piece_details', 'Admins\ReturnController@get_piece_details')->name('shipment.piece_details');

            Route::post('note/submit', 'Admins\ReturnController@return_note_create')->name('note.submit');
        });
        Route::prefix('receive')->name('receive.')->group(function () {

            Route::get('', 'Admins\ReturnController@return_receive_deliveries_view')->name('index');
            Route::get('list', 'Admins\ReturnController@return_receive_deliveries_list')->name('list');
            Route::get('{id}/update', 'Admins\ReturnController@return_receive_update')->name('update');
            Route::get('{id}/update/list', 'Admins\ReturnController@return_receive_update_list')->name('update.list');
            Route::get('update/remove', 'Admins\ReturnController@return_receive_update_remove')->name('update.remove');
            Route::get('{id}/status', 'Admins\ReturnController@return_receive_status')->name('status');
            Route::post('status/submit', 'Admins\ReturnController@receive_return_status_submit')->name('status.submit');
            Route::post('status/submit_all', 'Admins\ReturnController@receive_return_status_submit_all')->name('status.submit_all');
            Route::post('status/delivered', 'Admins\ReturnController@return_status_delivered')->name('status.delivered');
            Route::get('status/list', 'Admins\ReturnController@return_receive_status_list')->name('status.list');
            Route::post('reason', 'Admins\ReturnController@receive_return_reason')->name('reason');
            Route::post('rn.print', 'Admins\ReturnController@rrd_print')->name('rn.print');
            Route::post('shipments', 'Admins\ReturnController@receive_return_shipments')->name('shipments');
            Route::post('upload_image', 'Admins\ReturnController@receive_return_note_image_upload')->name('upload_image');
            Route::post('undelivered/print', 'Admins\ReturnController@return_undelivered_print')->name('undelivered.print');
            Route::post('reassign_rider', 'Admins\ReturnController@reassign_rider')->name('reassign_rider');
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\ReturnController@history_index')->name('index');
            Route::get('list', 'Admins\ReturnController@history_list')->name('list');
            Route::post('shipments', 'Admins\ReturnController@history_shipments')->name('shipments');
            Route::post('delivered_shipments', 'Admins\ReturnController@history_delivered_shipments')->name('delivered_shipments');
            Route::post('get_images', 'Admins\ReturnController@history_get_images')->name('get_images');
            Route::post('delete_image', 'Admins\ReturnController@history_delete_image')->name('delete_image');
            Route::post('delete_lastimage', 'Admins\ReturnController@history_delete_lastimage')->name('delete_lastimage');
        });


        Route::prefix('return_shipments')->name('return_shipments.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_shipments_index')->name('index');
            Route::get('list', 'Admins\ReturnController@return_shipments_list')->name('list');
            Route::get('notes', 'Admins\ReturnController@return_notes_list')->name('notes');
        });

        Route::prefix('cx_sales')->name('cx_sales.')->group(function () {
            Route::get('', 'Admins\ReturnController@cx_sales_index')->name('index');
            Route::get('list', 'Admins\ReturnController@cx_sales_list')->name('list');
        });
        Route::prefix('return_deliveries')->name('return_deliveries.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_deliveries_index')->name('index');
            Route::get('list', 'Admins\ReturnController@return_deliveries_list')->name('list');
            Route::get('app_shipment_list', 'Admins\ReturnController@return_deliveries_app_shipments_list')->name('app_shipment_list');
            Route::get('dbf_shipment_list', 'Admins\ReturnController@return_deliveries_dbf_shipments_list')->name('dbf_shipment_list');
        });

        Route::prefix('rcp_agent')->name('rcp_agent.')->group(function () {
            Route::get('', 'Admins\ReturnController@new_rcp_agent_index')->name('index');
            Route::get('list', 'Admins\ReturnController@new_rcp_agent_list')->name('list');
            Route::post('data', 'Admins\ReturnController@new_rcp_agent_data')->name('data');
        });

        Route::prefix('rcp_agent_cn')->name('rcp_agent_cn.')->group(function () {
            Route::get('', 'Admins\ReturnController@new_rcp_agent_shipments_index')->name('index');
            Route::get('list', 'Admins\ReturnController@new_rcp_agent_shipments_list')->name('list');
        });

        Route::prefix('new_rcp_agent')->name('new_rcp_agent.')->group(function () {
            Route::get('', 'Admins\ReturnController@new_rcp_agent_index')->name('index');
            Route::get('list', 'Admins\ReturnController@new_rcp_agent_list')->name('list');
            Route::post('data', 'Admins\ReturnController@new_rcp_agent_data')->name('data');
        });

        Route::prefix('new_rcp_agent_shipments')->name('new_rcp_agent_shipments.')->group(function () {
            Route::get('', 'Admins\ReturnController@new_rcp_agent_shipments_index')->name('index');
            Route::get('list', 'Admins\ReturnController@new_rcp_agent_shipments_list')->name('list');
        });

        Route::prefix('revert')->name('revert.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_revert_index')->name('index');
            Route::post('shipment_details', 'Admins\ReturnController@return_revert_shipment_details')->name('shipment_details');
            Route::post('submit', 'Admins\ReturnController@return_revert_submit')->name('submit');
        });

        Route::prefix('rider_request')->name('rider_request.')->group(function () {
            Route::get('', 'Admins\ReturnController@rider_request_note_index')->name('index');
            Route::get('list', 'Admins\ReturnController@rider_request_note_list')->name('list');
            Route::post('shipments', 'Admins\ReturnController@request_note_shipments')->name('shipments');
            Route::get('{id}/approve', 'Admins\ReturnController@request_note_approve')->name('approve');
            Route::get('{id}/reject', 'Admins\ReturnController@request_note_reject')->name('reject');
            Route::get('{id}/update', 'Admins\ReturnController@request_note_update')->name('update');
            Route::get('{id}/update/list', 'Admins\ReturnController@request_note_update_list')->name('update.list');
            Route::post('update/remove', 'Admins\ReturnController@request_note_remove')->name('update.remove');
            Route::post('update/remove_bulk', 'Admins\ReturnController@request_note_remove_bulk')->name('update.remove.bulk');
            Route::post('/add/tracking_number', 'Admins\ReturnController@add_shipments_in_request_note')->name('add.shipments');
        });

        Route::prefix('return_confirm_otp')->name('return_confirm_otp.')->group(function () {
            Route::get('', 'Admins\ReturnController@return_confirm_otp_index')->name('index');
            Route::get('list', 'Admins\ReturnController@return_confirm_otp_list')->name('list');
        });

        Route::prefix('shipper_return_receiving')->name('shipper_return_receiving.')->group(function () {
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\ReturnController@return_sheet_history_index')->name('index');
                Route::get('list', 'Admins\ReturnController@return_sheet_history_list')->name('list');
            });
        });
    });

    Route::prefix('debriefing')->name('debriefing.')->group(function () {
        Route::prefix('supervisor')->name('supervisor.')->group(function () {
            Route::get('', 'Admins\LastMileDebriefingController@supervisor_view')->name('index');
            Route::get('list', 'Admins\LastMileDebriefingController@supervisor_list')->name('list');
            Route::post('agents', 'Admins\LastMileDebriefingController@supervisor_agents')->name('agents');
            Route::post('assign_agents', 'Admins\LastMileDebriefingController@supervisor_assign_agents')->name('assign_agents');
            Route::post('get_undelivered_shipments', 'Admins\LastMileDebriefingController@get_undelivered_shipments')->name('get_undelivered_shipments');
            Route::post('send_sms', 'Admins\LastMileDebriefingController@send_sms_to_undelivered_shipments')->name('send_sms');
        });
        Route::prefix('agents_call_monitoring')->name('agents_call_monitoring.')->group(function () {
            Route::get('', 'Admins\LastMileDebriefingController@agents_call_monitoring_view')->name('index');
            Route::get('list', 'Admins\LastMileDebriefingController@agents_call_monitoring_list')->name('list');
        });
        Route::prefix('caller_agent')->name('caller_agent.')->group(function () {
            Route::get('', 'Admins\LastMileDebriefingController@caller_agent_view')->name('index');
            Route::post('next', 'Admins\LastMileDebriefingController@caller_agent_next')->name('next');
            Route::post('skip', 'Admins\LastMileDebriefingController@caller_agent_skip')->name('skip');
            Route::post('follow_up/{id}', 'Admins\LastMileDebriefingController@caller_agent_follow_up')->name('follow_up');
            Route::post('start', 'Admins\LastMileDebriefingController@caller_agent_start')->name('start');
            Route::post('break', 'Admins\LastMileDebriefingController@caller_agent_break')->name('break');
            Route::post('end', 'Admins\LastMileDebriefingController@caller_agent_end')->name('end');
        });
    });

    Route::prefix('cargo')->name('cargo.')->group(function () {
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@pending_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@pending_list')->name('list');
        });

        Route::prefix('create')->name('create.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@create_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@create_shipment_details')->name('shipment_details');
            Route::post('consignment_details', 'Admins\AdminCargoController@create_consignment_details')->name('consignment_details');
            Route::get('seal_number', 'Admins\AdminCargoController@create_consignment_seal_number')->name('seal_number');
            Route::post('', 'Admins\AdminCargoController@create_store')->name('store');
        });

        Route::prefix('in_transit')->name('in_transit.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@in_transit_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@in_transit_list')->name('list');
            Route::post('print', 'Admins\AdminCargoController@in_transit_print')->name('print');
            Route::post('junctions', 'Admins\AdminCargoController@in_transit_junctions')->name('junctions');
            Route::post('details', 'Admins\AdminCargoController@in_transit_details')->name('details');
            Route::post('receive_at_link', 'Admins\AdminCargoController@in_transit_receive_at_link')->name('receive_at_link');
            Route::post('forwarding_details', 'Admins\AdminCargoController@in_transit_forwarding_details')->name('forwarding_details');
            Route::post('update', 'Admins\AdminCargoController@in_transit_update')->name('update');
            Route::post('receive', 'Admins\AdminCargoController@in_transit_receive')->name('receive');
            Route::post('shipments', 'Admins\AdminCargoController@in_transit_shipments')->name('shipments');
            Route::post('short_received_shipments', 'Admins\AdminCargoController@in_transit_short_received_shipments')->name('short_received_shipments');
            Route::post('lost', 'Admins\AdminCargoController@in_transit_lost')->name('lost');
            Route::post('send_details', 'Admins\AdminCargoController@send_from_junction')->name('send_details');
            Route::post('send_from_junction', 'Admins\AdminCargoController@in_transit_send_from_junction')->name('send_from_junction');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@receive_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminCargoController@receive_shipment_details')->name('shipment_details');
            Route::post('short_received', 'Admins\AdminCargoController@receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminCargoController@receive_store')->name('store');

            Route::prefix('quick')->name('quick.')->group(function () {
                Route::get('', 'Admins\AdminCargoController@quick_receive_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminCargoController@quick_receive_shipment_details')->name('shipment_details');
                Route::post('', 'Admins\AdminCargoController@quick_receive_store')->name('store');
                Route::get('list', 'Admins\AdminCargoController@quick_receive_list_index')->name('list.index');
                Route::get('list/ajax', 'Admins\AdminCargoController@quick_receive_list_ajax')->name('list.ajax');
                Route::post('list/ajax', 'Admins\AdminCargoController@quick_receive_list_details')->name('list.details');
            });
        });
        Route::post('piece_details', 'Admins\AdminCargoController@cargo_piece_details')->name('piece_details');
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@history_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@history_list')->name('list');
            Route::post('shipments', 'Admins\AdminCargoController@history_shipments')->name('shipments');
            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
        });

        Route::prefix('draft')->name('draft.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@draft_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@draft_list')->name('list');
            Route::post('add', 'Admins\AdminCargoController@draft_add')->name('add');
            Route::post('shipments', 'Admins\AdminCargoController@draft_shipments')->name('shipments');
            Route::prefix('edit')->name('edit.')->group(function () {
                Route::get('{draft}', 'Admins\AdminCargoController@edit_draft_index')->name('index');
                Route::get('{draft}/list', 'Admins\AdminCargoController@edit_draft_list')->name('list');
                Route::post('update', 'Admins\AdminCargoController@draft_update')->name('update');
            });
        });

        Route::prefix('supply_chain')->name('supply_chain.')->group(function () {
            Route::get('', 'Admins\OrderManagementController@supply_chain_index')->name('supply_chain_index');
            Route::get('list', 'Admins\OrderManagementController@supply_chain_list')->name('supply_chain_list');

            Route::prefix('shipment_on_hold')->name('shipment_on_hold.')->group(function () {
                Route::get('', 'Admins\AdminSupplyChainController@shipment_on_hold_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminSupplyChainController@shipment_on_hold_details')->name('shipment_details');
                Route::post('store', 'Admins\AdminSupplyChainController@shipment_on_hold_store')->name('store');

                //history
                Route::prefix('history')->name('history.')->group(function () {
                    Route::get('', 'Admins\AdminSupplyChainController@shipment_on_hold_history')->name('index');
                    Route::get('list', 'Admins\AdminSupplyChainController@shipment_on_hold_history_list')->name('list');
                    Route::post('allow_dispatch_delivery', 'Admins\AdminSupplyChainController@allow_dispatch_delivery')->name('allow_dispatch_delivery');
                });
            });
        });

        Route::prefix('mapping')->name('mapping.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@mapping_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@mapping_list')->name('list');
            Route::post('store', 'Admins\AdminCargoController@mapping_store')->name('store');
            Route::post('edit', 'Admins\AdminCargoController@mapping_edit')->name('edit');
            Route::post('update', 'Admins\AdminCargoController@mapping_edit_update')->name('update');
            //            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
            Route::prefix('manifest')->name('manifest.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@manifest_mapping_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@manifest_mapping_list')->name('list');
                Route::post('store', 'Admins\AdminCargoManifestController@manifest_mapping_store')->name('store');
                Route::get('edit/{id}', 'Admins\AdminCargoManifestController@manifest_mapping_edit')->name('edit');
                Route::post('update/{id}', 'Admins\AdminCargoManifestController@manifest_mapping_edit_update')->name('update');
                Route::post('status', 'Admins\AdminCargoManifestController@manifest_mapping_status')->name('status');
            });
        });
    });
    Route::prefix('master_cargo')->name('master_cargo.')->group(function () {
        Route::prefix('bag')->name('bag.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@pending_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@pending_list')->name('list');
            });

            Route::prefix('create')->name('create.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@create_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminMasterCargoController@create_shipment_details')->name('shipment_details');
                Route::post('bag_details', 'Admins\AdminMasterCargoController@create_bag_details')->name('bag_details');
                Route::post('piece_details', 'Admins\AdminMasterCargoController@bag_piece_details')->name('piece_details');
                Route::get('seal_number', 'Admins\AdminMasterCargoController@create_bag_seal_number')->name('seal_number');
                Route::post('', 'Admins\AdminMasterCargoController@create_store')->name('store');

                Route::prefix('open_bag')->name('open_bag.')->group(function () {
                    Route::get('', 'Admins\AdminMasterCargoController@create_open_bag_index')->name('index');
                    Route::post('shipment_details', 'Admins\AdminMasterCargoController@create_open_bag_shipment_details')->name('shipment_details');
                    Route::post('', 'Admins\AdminMasterCargoController@create_open_bag_store')->name('store');
                });
            });
            Route::post('update_seal_number', 'Admins\AdminMasterCargoController@update_seal_number')->name('update_seal_number');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@history_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@history_list')->name('list');
            });

            Route::prefix('in_transit')->name('in_transit.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_index')->name('index');
                Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_list')->name('list');
                Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_short_received')->name('short_received');
                Route::post('receive', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bag_receive')->name('receive');
            });

            Route::prefix('receive')->name('receive.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_index')->name('index');
                Route::post('shipment_details', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_shipment_details')->name('shipment_details');
                Route::post('piece_details', 'Admins\AdminMasterCargoController@bag_piece_details')->name('piece_details');
                Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_short_received')->name('short_received');
                Route::post('', 'Admins\AdminMasterCargoController@master_cargo_bag_receive_store')->name('store');

                Route::prefix('quick')->name('quick.')->group(function () {
                    Route::get('', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_index')->name('index');
                    Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_bag_details')->name('bag_details');
                    Route::post('', 'Admins\AdminMasterCargoController@master_cargo_bag_quick_receive_store')->name('store');
                });
            });
        });
        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_pending_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_pending_list')->name('list');
            Route::post('shipments', 'Admins\AdminMasterCargoController@master_cargo_pending_shipments')->name('shipments');
        });

        Route::prefix('create')->name('create.')->group(function () {
            Route::get('{id?}', 'Admins\AdminMasterCargoController@master_cargo_create_index')->name('index');
            Route::post('bag_details', 'Admins\AdminMasterCargoController@create_master_cargo_bag_details')->name('bag_details');
            Route::post('bag_cargo_details', 'Admins\AdminMasterCargoController@create_master_cargo_details')->name('cargo_details');
            Route::post('', 'Admins\AdminMasterCargoController@master_cargo_create_store')->name('store');
        });
        Route::post('all_junctions', 'Admins\AdminMasterCargoController@master_cargo_in_transit_all_junctions')->name('all_junctions');

        Route::prefix('in_transit')->name('in_transit.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_in_transit_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_in_transit_list')->name('list');
            Route::post('lost', 'Admins\AdminMasterCargoController@master_cargo_in_transit_lost')->name('lost');
            Route::post('bags', 'Admins\AdminMasterCargoController@master_cargo_in_transit_bags')->name('bags');
            Route::post('short_received_bags', 'Admins\AdminMasterCargoController@master_cargo_in_transit_short_received_bags')->name('short_received_bags');
            Route::post('shipments', 'Admins\AdminMasterCargoController@master_cargo_in_transit_shipments')->name('shipments');


            Route::post('print', 'Admins\AdminMasterCargoController@master_cargo_in_transit_print')->name('print');
            Route::post('junctions', 'Admins\AdminMasterCargoController@master_cargo_in_transit_junctions')->name('junctions');
            Route::post('details', 'Admins\AdminMasterCargoController@master_cargo_in_transit_details')->name('details');
            Route::post('receive_at_link', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive_at_link')->name('receive_at_link');
            Route::post('receive_at_link/store', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive_at_link_store')->name('receive_at_link.store');
            Route::post('receive', 'Admins\AdminMasterCargoController@master_cargo_in_transit_receive')->name('receive');
        });

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_receive_index')->name('index');
            Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_receive_bag_details')->name('bag_details');
            Route::post('short_received', 'Admins\AdminMasterCargoController@master_cargo_receive_short_received')->name('short_received');
            Route::post('', 'Admins\AdminMasterCargoController@master_cargo_receive_store')->name('store');

            Route::prefix('quick')->name('quick.')->group(function () {
                Route::get('', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_index')->name('index');
                Route::post('bag_details', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_bag_details')->name('bag_details');
                Route::post('', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_store')->name('store');
                Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_index')->name('list.index');
                Route::get('list/ajax', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_ajax')->name('list.ajax');
                Route::post('list/ajax', 'Admins\AdminMasterCargoController@master_cargo_quick_receive_list_details')->name('list.details');
            });
        });

        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_history_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_history_list')->name('list');
        });

        Route::prefix('received')->name('received.')->group(function () {
            Route::get('', 'Admins\AdminMasterCargoController@master_cargo_received_index')->name('index');
            Route::get('list', 'Admins\AdminMasterCargoController@master_cargo_received_list')->name('list');
        });

        Route::prefix('mapping')->name('mapping.')->group(function () {
            Route::get('', 'Admins\AdminCargoController@mapping_index')->name('index');
            Route::get('list', 'Admins\AdminCargoController@mapping_list')->name('list');
            Route::post('store', 'Admins\AdminCargoController@mapping_store')->name('store');
            Route::post('edit', 'Admins\AdminCargoController@mapping_edit')->name('edit');
            Route::post('update', 'Admins\AdminCargoController@mapping_edit_update')->name('update');
            //            Route::post('print', 'Admins\AdminCargoController@history_cargo_print')->name('print');
        });
    });
    Route::prefix('cargo_manifest')->name('cargo_manifest.')->group(function () {
        Route::post('print', 'Admins\AdminCargoManifestController@cargo_manifest_in_transit_print')->name('print');
        Route::prefix('bags')->name('bags.')->group(function () {
            Route::prefix('pending')->name('pending.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@pending_bag_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@pending_bag_list')->name('list');
            });

            Route::prefix('create')->name('create.')->group(function () {

                Route::get('', 'Admins\AdminCargoManifestController@create_index')->name('index');
                Route::post('count_per_hub', 'Admins\AdminCargoManifestController@count_per_hub')->name('count_per_hub');
                Route::post('shipment_details', 'Admins\AdminCargoManifestController@create_shipment_details')->name('shipment_details');
                //Route::post('shipment_details', 'Admins\AdminCargoManifestController@create_shipment_details_old')->name('shipment_details'); // old_one
                Route::post('bag_details', 'Admins\AdminCargoManifestController@create_bag_details')->name('bag_details');
                Route::get('seal_number', 'Admins\AdminCargoManifestController@create_bag_seal_number')->name('seal_number');
                Route::post('', 'Admins\AdminCargoManifestController@create_store')->name('store');
                //Route::post('', 'Admins\AdminCargoManifestController@create_store_old')->name('store'); // old one

                Route::prefix('open_bag')->name('open_bag.')->group(function () {
                    Route::get('', 'Admins\AdminCargoManifestController@create_open_bag_index')->name('index');
                    //Route::post('shipment_details', 'Admins\AdminCargoManifestController@create_open_bag_shipment_details')->name('shipment_details');
                    Route::post('', 'Admins\AdminCargoManifestController@create_open_bag_store')->name('store');
                });
            });
            Route::post('piece_details', 'Admins\AdminCargoManifestController@bag_piece_details')->name('piece_details');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@history_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@history_list')->name('list');
                Route::post('lost_shipments', 'Admins\AdminCargoManifestController@history_lost_shipments')->name('lost_shipments');
            });
            Route::prefix('sack_bag')->name('sack_bag.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@sack_bag_index')->name('index');
                Route::get('list', 'Admins\AdminCargoManifestController@sack_bag_list')->name('list');
                Route::post('store', 'Admins\AdminCargoManifestController@add_sack_bag')->name('store');
                Route::post('sack_bag_check', 'Admins\AdminCargoManifestController@sack_bag_no_check')->name('sack_bag_check');
                Route::post('no_check_for_cb', 'Admins\AdminCargoManifestController@sack_bag_no_check_for_cb')->name('no_check_for_cb');
                
            });
        });
        Route::get('/', 'Admins\AdminCargoManifestController@manifest_index')->name('index');
        Route::get('/list', 'Admins\AdminCargoManifestController@manifest_list')->name('list');
        Route::get('/create', 'Admins\AdminCargoManifestController@create_manifest')->name('create');
        Route::post('bag/details', 'Admins\AdminCargoManifestController@bag_details')->name('bag_details');
        Route::post('cargo/details', 'Admins\AdminCargoManifestController@cargo_details')->name('cargo_details');
        Route::post('/store', 'Admins\AdminCargoManifestController@store_manifest')->name('store');
        Route::post('/update/seal_number', 'Admins\AdminCargoManifestController@update_seal_number')->name('update.seal_number');
        Route::post('/junctions', 'Admins\AdminCargoManifestController@junctions_info')->name('junctions_info');
        Route::post('/vehicle', 'Admins\AdminCargoManifestController@vehicle_info')->name('vehicle_info');
        Route::post('/remarks_info', 'Admins\AdminCargoManifestController@remarks_info')->name('remarks_info');
        Route::post('/transitted_shipments', 'Admins\AdminCargoManifestController@transitted_shipments')->name('transitted_shipments');
        Route::post('short_received_shipments', 'Admins\AdminCargoManifestController@short_received_shipments')->name('short_received_shipments');

        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'Admins\AdminCargoManifestController@receive_bag_index')->name('index');
            //Route::get('', 'Admins\AdminCargoManifestController@receive_bag_index_old')->name('index'); // old_one
            Route::post('bag_details', 'Admins\AdminCargoManifestController@receive_bag_details')->name('bag_details');
            //Route::post('bag_details', 'Admins\AdminCargoManifestController@receive_bag_details_old')->name('bag_details'); // old one
            Route::post('store', 'Admins\AdminCargoManifestController@receive_bag_store')->name('store');
            //Route::post('store', 'Admins\AdminCargoManifestController@receive_bag_store_old')->name('store'); // old one

            Route::prefix('bag')->name('bag.')->group(function () {
                Route::get('', 'Admins\AdminCargoManifestController@receive_bag_shipments_index')->name('index');
                //Route::get('', 'Admins\AdminCargoManifestController@receive_bag_shipments_index_old')->name('index'); //old one
                Route::post('details', 'Admins\AdminCargoManifestController@receive_bag_shipments_details')->name('details');
                //Route::post('details', 'Admins\AdminCargoManifestController@receive_bag_shipments_details_old')->name('details'); // old one
                Route::post('details/return', 'Admins\AdminCargoManifestController@receive_bag_shipments_details_return')->name('details.return');
                Route::post('store', 'Admins\AdminCargoManifestController@receive_bag_shipments_store')->name('store');
                //Route::post('store', 'Admins\AdminCargoManifestController@receive_bag_shipments_store_old')->name('store'); // old one
            });
        });

        Route::get('history', 'Admins\AdminCargoManifestController@manifest_history')->name('history');
        Route::get('history/list', 'Admins\AdminCargoManifestController@manifest_history_list')->name('history.list');
        Route::post('history/bags', 'Admins\AdminCargoManifestController@manifest_bags')->name('history.bags');
        Route::post('history/short_received_bags', 'Admins\AdminCargoManifestController@cargo_short_received_bags')->name('history.short_received_bags');
        Route::post('history/shipments', 'Admins\AdminCargoManifestController@cargo_bag_shipments')->name('history.shipments');

        Route::prefix('draft')->name('draft.')->group(function () {
            Route::get('/list', 'Admins\AdminCargoManifestController@manifest_draft')->name('list');
            Route::post('/delete', 'Admins\AdminCargoManifestController@manifest_draft_delete')->name('delete');
            Route::get('setting/list', 'Admins\AdminCargoManifestController@manifest_draft_setting_list')->name('setting.list');
            Route::get('setting', 'Admins\AdminCargoManifestController@manifest_draft_setting')->name('setting');
            Route::post('update', 'Admins\AdminCargoManifestController@manifest_draft_update')->name('update');
        });

        Route::post('/total_bags', 'Admins\AdminCargoManifestController@total_bags_info')->name('total_bags');
    });
    Route::prefix('dispute')->name('dispute.')->group(function () {
        Route::get('', 'Admins\DisputeController@dispute_index')->name('index');
        Route::get('list', 'Admins\DisputeController@dispute_list')->name('list');
        Route::post('create', 'Admins\DisputeController@dispute_create')->name('create');
        Route::post('get/shipments', 'Admins\DisputeController@get_shipments')->name('get.shipments');
        Route::post('resolve', 'Admins\DisputeController@resolve_dispute')->name('resolve');
        Route::post('bulk_resolve', 'Admins\DisputeController@bulk_resolve_dispute')->name('bulk_resolve');
        Route::post('update', 'Admins\DisputeController@update_dispute_view')->name('update');
        Route::put('update/submit', 'Admins\DisputeController@update_dispute')->name('update.submit');
        Route::post('data', 'Admins\DisputeController@get_data')->name('data');
        Route::post('create/universal', 'Admins\DisputeController@dispute_create_universal')->name('create.universal');

        Route::prefix('shipments')->name('shipments.')->group(function () {
            Route::get('', 'Admins\V2AdminDisputeShipmentsController@index')->name('index');
            Route::get('list', 'Admins\V2AdminDisputeShipmentsController@list')->name('list');
            Route::post('submit', 'Admins\V2AdminDisputeShipmentsController@add_submit')->name('submit');
            Route::post('update', 'Admins\V2AdminDisputeShipmentsController@dispute_update')->name('update');
            Route::post('images', 'Admins\V2AdminDisputeShipmentsController@dispute_images')->name('images');
            Route::post('bulk_in_process', 'Admins\V2AdminDisputeShipmentsController@bulk_in_process')->name('bulk_in_process');
            Route::post('bulk_resolved', 'Admins\V2AdminDisputeShipmentsController@bulk_resolved')->name('bulk_resolved');
            Route::post('excel_upload', 'Admins\V2AdminDisputeShipmentsController@excel_upload')->name('excel_upload');
        });
    });

    Route::prefix('tracking')->name('tracking.')->group(function () {
        Route::get('{tracking_number?}', 'Admins\AdminTrackingController@index')->name('index');
        Route::post('track', 'Admins\AdminTrackingController@track')->name('track');
        Route::post('track_v2', 'Admins\AdminTrackingController@track_v2')->name('track_v2');
        Route::post('rider_information', 'Admins\AdminTrackingController@rider_information')->name('rider_information');
        Route::post('rider_unresponsive_status', 'Admins\AdminTrackingController@rider_unresponsive_status')->name('rider_unresponsive_status');
        Route::post('cargo_consignment_details', 'Admins\AdminTrackingController@cargo_consignment_details')->name('cargo_consignment_details');
        Route::post('pieces_print', 'Admins\AdminTrackingController@pieces_print')->name('pieces_print');
        Route::post('estimation_check', 'Admins\AdminTrackingController@estimation_check')->name('estimation_check');
        Route::prefix('shipment_position')->name('shipment_position.')->group(function () {
            Route::get('track', 'Admins\AdminTrackingController@shipment_position_index')->name('track');
            Route::post('upload', 'Admins\AdminTrackingController@shipment_position_upload')->name('upload');
            Route::get('list', 'Admins\AdminTrackingController@shipment_position_list')->name('list');
        });

    });

    Route::prefix('quick_tracking')->name('quick_tracking.')->group(function () {
        Route::get('', 'Admins\AdminTrackingController@quick_tracking_index')->name('index');
        Route::post('info', 'Admins\AdminTrackingController@quick_tracking_shipment_info')->name('info');
        Route::post('update', 'Admins\AdminTrackingController@quick_tracking_shipment_remark_update')->name('update_remarks');

    });


    Route::prefix('cx_quick_tracking')->name('cx_quick_tracking.')->group(function () {
        Route::get('', 'Admins\AdminTrackingController@cx_quick_tracking_index')->name('cx_index');
        Route::get('list', 'Admins\AdminTrackingController@cx_quick_tracking_list')->name('cx_list');
        Route::post('update', 'Admins\AdminTrackingController@cx_quick_tracking_update_consignee_info_and_special_instructions')->name('update');
    });


    Route::prefix('crm_permission')->name('crm_permission.')->group(function () {
        Route::get('roles/permissions', 'Admins\UserManagementController@crm_role_permission_index')->name('index');
        Route::get('roles/add', 'Admins\UserManagementController@role_add_index')->name('add');
        Route::post('list', 'Admins\UserManagementController@crm_role_permission_list')->name('list');
        Route::get('bulk_add/{id}', 'Admins\UserManagementController@crm_update_index')->name('bulk_add');
        Route::post('bulk_add', 'Admins\UserManagementController@role_crm_bulk_add_store')->name('bulk_add.store');
        Route::get('bulk_remove/{id}', 'Admins\UserManagementController@delete_crm_update_index')->name('bulk_remove');
        Route::get('bulk_remove', 'Admins\UserManagementController@delete_bulk_remove_store')->name('bulk_remove.remove');

    });
    Route::prefix('user_management')->name('user_management.')->group(function () {
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('', 'Admins\UserManagementController@user_index')->name('index');
            Route::post('rejoin', 'Admins\UserManagementController@rejoin')->name('rejoin');
            Route::get('list', 'Admins\UserManagementController@user_list')->name('list');
            
            Route::get('email', 'Admins\UserManagementController@user_email')->name('email');
            Route::get('trax_id', 'Admins\UserManagementController@user_trax_id')->name('trax_id');
            Route::post('status', 'Admins\UserManagementController@user_status')->name('status');
            Route::post('assign_hubs', 'Admins\UserManagementController@user_assign_hub')->name('assign_hubs');
            Route::post('add_management_users', 'Admins\UserManagementController@add_management_users')->name('add_management_users');

            Route::get('validate_phone', 'Admins\UserManagementController@validate_phone')->name('validate_phone');
            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\UserManagementController@user_add_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_add_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function () {
                Route::get('', 'Admins\UserManagementController@user_update_index')->name('index');
                Route::post('', 'Admins\UserManagementController@user_update_store')->name('store');
            });

            Route::post('user_info', 'Admins\UserManagementController@user_info')->name('user_info');
            Route::post('phone_update', 'Admins\UserManagementController@user_phone_update')->name('phone_update');
            Route::post('lost_hub_user_shipment', 'Admins\UserManagementController@lost_hub_user_shipment')->name('lost_hub_user_shipment');
            Route::get('get_lost_hub_user_shipment', 'Admins\UserManagementController@get_lost_hub_user_shipment')->name('get_lost_hub_user_shipment');
            Route::post('set_hub_access', 'Admins\UserManagementController@set_hub_access')->name('set_hub_access');
            Route::get('get_hub_access', 'Admins\UserManagementController@get_hub_access')->name('get_hub_access');

        });

        Route::prefix('fuel_management')->name('fuel_management.')->group(function () {
            Route::get('', 'Admins\Fuel\FuelManagementController@fuel_index')->name('index');
            Route::get('list', 'Admins\Fuel\FuelManagementController@fuel_list')->name('list');
            Route::get('request', 'Admins\Fuel\FuelManagementController@request_create')->name('create');
            Route::post('request', 'Admins\Fuel\FuelManagementController@request_store')->name('store');
            Route::post('request/approve', 'Admins\Fuel\FuelManagementController@request_approve')->name('approve');
            Route::post('request/edit', 'Admins\Fuel\FuelManagementController@request_edit')->name('edit');
            Route::get('request/search/card', 'Admins\Fuel\FuelManagementController@request_search_by_card')->name('search_by_card');
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\Fuel\FuelManagementController@request_history')->name('index');
            });
        });

        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('', 'Admins\UserManagementController@role_index')->name('index');
            Route::get('list', 'Admins\UserManagementController@role_list')->name('list');
            Route::post('enable_disable', 'Admins\UserManagementController@role_status')->name('enable_disable');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\UserManagementController@role_add_index')->name('index');
                Route::post('', 'Admins\UserManagementController@role_add_store')->name('store');
                Route::post('duplicate_role', 'Admins\UserManagementController@role_duplicate')->name('duplicate_role');
            });

            Route::prefix('permissions')->name('permissions.')->group(function () {
                Route::get('', 'Admins\UserManagementController@role_permission_index')->name('index');
                Route::post('module_permission', 'Admins\UserManagementController@module_permission')->name('modulepermission');
                Route::post('list', 'Admins\UserManagementController@role_permission_list')->name('list');
                Route::post('store', 'Admins\UserManagementController@module_permission_update_store')->name('store');
            });

            Route::prefix('update/{id}')->name('update.')->group(function () {
                Route::get('', 'Admins\UserManagementController@role_update_index')->name('index');
                Route::post('', 'Admins\UserManagementController@role_update_store')->name('store');
            });
            Route::get('bulk_add/{id}', 'Admins\UserManagementController@role_bulk_add_index')->name('bulk_add');
            Route::post('bulk_add', 'Admins\UserManagementController@role_bulk_add_store')->name('bulk_add.store');

            Route::get('bulk_remove/{id}', 'Admins\UserManagementController@role_bulk_remove_index')->name('bulk_remove');
            Route::post('bulk_remove', 'Admins\UserManagementController@role_bulk_remove_store')->name('bulk_remove.store');
        });

        Route::prefix('user_requests')->name('user_requests.')->group(function () {
            Route::get('', 'Admins\AdminUserRequestController@user_requests_index')->name('index');
            Route::get('list', 'Admins\AdminUserRequestController@user_requests_list')->name('list');
            Route::get('email', 'Admins\AdminUserRequestController@user_email')->name('email');
            Route::get('trax_id', 'Admins\AdminUserRequestController@user_trax_id')->name('trax_id');
            Route::post('assign_hubs', 'Admins\AdminUserRequestController@user_assign_hub')->name('assign_hubs');
            Route::get('cnic', 'Admins\AdminUserRequestController@user_cnic')->name('cnic');

            Route::prefix('add')->name('add.')->group(function () {
                Route::get('', 'Admins\AdminUserRequestController@user_request_add_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@user_add_store')->name('store');
            });
            Route::prefix('verify/{id}')->name('verify.')->group(function () {
                Route::get('', 'Admins\AdminUserRequestController@verify_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@verify_store')->name('store');
            });
            Route::prefix('save/{id}')->name('save.')->group(function () {
                Route::get('', 'Admins\AdminUserRequestController@user_save_index')->name('index');
                Route::post('', 'Admins\AdminUserRequestController@user_save')->name('store');
            });
            Route::post('forward', 'Admins\AdminUserRequestController@forward')->name('forward');
        });
        Route::get('/logs/index', 'UserRoleManagementLogController@index')->name('index');
        Route::get('/logs/list', 'UserRoleManagementLogController@list')->name('logs.list');

    });

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::prefix('outstanding_sdn')->name('outstanding_sdn.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_sdn_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_sdn_list')->name('list');
            Route::post('dncc', 'Admins\AdminFinanceController@outstanding_sdn_dncc')->name('dncc');
            Route::post('dncc/print', 'Admins\AdminFinanceController@outstanding_sdn_dncc_print')->name('dncc.print');
            Route::post('shipments/delivered', 'Admins\AdminFinanceController@outstanding_sdn_shipments_delivered')->name('shipments.delivered');
            Route::get('delivery_notes_list', 'Admins\AdminFinanceController@outstanding_sdn_delivery_notes_list')->name('delivery_notes_list');
            Route::post('reconcile_delivery_notes', 'Admins\AdminFinanceController@outstanding_sdn_reconcile_delivery_notes')->name('reconcile_delivery_notes');
            Route::post('reconcile_delivery_notes_excel', 'Admins\AdminFinanceController@outstanding_sdn_reconcile_delivery_notes_excel')->name('reconcile_delivery_notes_excel');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@outstanding_sdn_export_to_excel')->name('export_to_excel');
            Route::post('deposit_slip_list', 'Admins\AdminFinanceController@outstanding_sdn_edit_deposit_slip')->name('deposit_slip_list');
            Route::post('edit', 'Admins\AdminFinanceController@outstanding_sdn_edit_deposit_slip_submit')->name('edit');
        });

        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@outstanding_shipments_list')->name('list');
            Route::put('resolved', 'Admins\AdminFinanceController@outstanding_shipments_resolved')->name('resolved');
            Route::post('bulk_resolved', 'Admins\AdminFinanceController@outstanding_shipments_bulk_resolved')->name('bulk_resolved');
            Route::put('adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_adjust_in_payment')->name('adjust_in_payment');
            Route::post('bulk_adjust_in_payment', 'Admins\AdminFinanceController@outstanding_shipments_bulk_adjust_in_payment')->name('bulk_adjust_in_payment');
            Route::post('dncc/print', 'Admins\AdminFinanceController@outstanding_shipments_dncc_print')->name('dncc.print');
            Route::post('sdn/print', 'Admins\AdminFinanceController@outstanding_shipments_sdn_print')->name('sdn.print');
            Route::get('walk_in_index', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_index')->name('walk_in_index');
            Route::get('walk_in_list', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_list')->name('walk_in_list');
            Route::put('walk_in_resolved', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_resolved')->name('walk_in_resolved');
            Route::post('walk_in_bulk_resolved', 'Admins\AdminFinanceController@outstanding_walk_in_shipments_bulk_resolved')->name('walk_in_bulk_resolved');
            Route::put('revert_request_shipments_check', 'Admins\AdminFinanceController@revert_request_shipments_check')->name('revert_request_shipments_check');
            Route::post('revert_request_submit', 'Admins\AdminFinanceController@revert_request_submit')->name('revert_request_submit');
            Route::get('revert_requested/{image_id}', 'Admins\AdminFinanceController@revert_requested_image')->name('revert_requested_image');
        });

        Route::prefix('change_shipment_amount')->name('change_shipment_amount.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@change_shipment_amount_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@change_shipment_amount_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@change_shipment_amount_store')->name('store');
        });

        Route::prefix('change_shipment_weight')->name('change_shipment_weight.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@change_shipment_weight_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@change_shipment_weight_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@change_shipment_weight_store')->name('store');
            Route::post('excel_store', 'Admins\AdminFinanceController@change_shipment_weight_excel_store')->name('excel_store');
            Route::post('calculate_amount', 'Admins\AdminFinanceController@change_shipment_weight_calculate_amount')->name('calculate_amount');
            Route::post('view_excel_store', 'Admins\AdminFinanceController@view_change_shipment_weight_excel_store')->name('view_excel_store');
        });

        Route::prefix('add_shipment_adjustment')->name('add_shipment_adjustment.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@add_shipment_adjustment_index')->name('index');
            Route::post('shipment_details', 'Admins\AdminFinanceController@add_shipment_adjustment_shipment_details')->name('shipment_details');
            Route::post('', 'Admins\AdminFinanceController@add_shipment_adjustment_store')->name('store');
            Route::post('bulk/store', 'Admins\AdminFinanceController@add_bulk_shipment_adjustment_store')->name('bulk_store');
        });

        Route::prefix('make_payments')->name('make_payments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@make_payments_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@make_payments_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@make_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@make_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@make_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('shipment_details', 'Admins\AdminFinanceController@make_payments_shipment_details')->name('shipment_details');
            Route::get('shipment_list', 'Admins\AdminFinanceController@make_payments_shipment_list')->name('shipment_list');
            Route::get('shipment_export_selected', 'Admins\AdminFinanceController@make_payments_shipment_export_selected')->name('shipment_export_selected');
            Route::post('verify', 'Admins\AdminFinanceController@make_payments_verify')->name('verify');
            Route::post('invoice', 'Admins\AdminFinanceController@make_payments_invoice')->name('invoice');
            Route::get('export_bank_order', 'Admins\AdminFinanceController@make_payments_export_bank_order')->name('export_bank_order');
            Route::post('store', 'Admins\AdminFinanceController@make_payments_store')->name('store');
            Route::get('stats_calculate', 'Admins\AdminFinanceController@make_payments_stats_calculate')->name('stats_calculate');
            Route::post('fetch_shipper_ibft_charges', 'Admins\AdminFinanceController@fetch_shipper_ibft_charges')->name('fetch_shipper_ibft_charges');

            Route::post('make_payments_store_new', 'Admins\AdminFinanceController@make_payments_store_new')->name('make_payments_store_new');
            Route::get('payment', 'Admins\AdminFinanceController@payment')->name('payment');
            Route::get('payment_list', 'Admins\AdminFinanceController@payment_list')->name('payment_list');
            Route::get('payment_list_remaining', 'Admins\AdminFinanceController@payment_list_remaining')->name('payment_list_remaining');
            Route::post('fetch_shipper_ibft_charges_new', 'Admins\AdminFinanceController@fetch_shipper_ibft_charges_new')->name('fetch_shipper_ibft_charges_new');
            Route::get('make_payments_shipment_export_selected_new', 'Admins\AdminFinanceController@make_payments_shipment_export_selected_new')->name('make_payments_shipment_export_selected_new');
        });

        Route::prefix('make_payments_pickup_wise')->name('make_payments_pickup_wise.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@make_payments_pickup_wise_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@make_payments_pickup_wise_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@make_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@make_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@make_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('shipment_details', 'Admins\AdminFinanceController@make_payments_shipment_details')->name('shipment_details');
            Route::get('shipment_list', 'Admins\AdminFinanceController@make_payments_shipment_list')->name('shipment_list');
            Route::get('shipment_export_selected', 'Admins\AdminFinanceController@make_payments_shipment_export_selected')->name('shipment_export_selected');
            Route::post('verify', 'Admins\AdminFinanceController@make_payments_verify')->name('verify');
            Route::get('export_bank_order', 'Admins\AdminFinanceController@make_payments_export_bank_order')->name('export_bank_order');
            Route::post('store', 'Admins\AdminFinanceController@make_payments_store')->name('store');
            Route::get('stats_calculate', 'Admins\AdminFinanceController@make_payments_stats_calculate')->name('stats_calculate');
        });

        Route::prefix('done_payments')->name('done_payments.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@done_payments_index')->name('index');
            Route::post('list', 'Admins\AdminFinanceController@done_payments_list')->name('list');
            Route::put('paid', 'Admins\AdminFinanceController@done_payments_paid')->name('paid');
            Route::put('tax_paid', 'Admins\AdminFinanceController@done_payments_tax_paid')->name('tax_paid');
            Route::put('reverted', 'Admins\AdminFinanceController@done_payments_reverted')->name('reverted');
            Route::post('delivered_shipments', 'Admins\AdminFinanceController@done_payments_delivered_shipments')->name('delivered_shipments');
            Route::post('returned_shipments', 'Admins\AdminFinanceController@done_payments_returned_shipments')->name('returned_shipments');
            Route::post('adjusted_shipments', 'Admins\AdminFinanceController@done_payments_adjusted_shipments')->name('adjusted_shipments');
            Route::post('arrival_shipment', 'Admins\AdminFinanceController@done_payments_arrival_shipment')->name('arrival_shipment');
            Route::post('details_print', 'Admins\AdminFinanceController@done_payments_details_print')->name('details_print');
            Route::post('details', 'Admins\AdminFinanceController@done_payments_details')->name('details');
            Route::put('update_details', 'Admins\AdminFinanceController@done_payments_update_details')->name('update_details');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@done_payments_export_to_excel')->name('export_to_excel');
            Route::get('generate_report_to_email', 'Admins\AdminFinanceController@done_payments_generate_report_to_email')->name('generate_report_to_email');
            Route::post('excel_store', 'Admins\AdminFinanceController@done_payments_excel_store')->name('excel_store');
            Route::get('view_status_history', 'Admins\AdminFinanceController@view_status_history')->name('view_status_history');
            Route::get('mark_settlement', 'Admins\AdminFinanceController@mark_settlement')->name('mark_settlement');
            Route::get('wallet_error_logs', 'Admins\AdminFinanceController@wallet_error_logs')->name('wallet_error_logs');
            
        });

        Route::prefix('invoices')->name('invoices.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@invoices_index')->name('index');
            Route::post('list', 'Admins\AdminFinanceController@invoices_list')->name('list');
            Route::post('slip', 'Admins\AdminFinanceController@invoices_slip')->name('slip');
            Route::post('slip/view', 'Admins\AdminFinanceController@invoices_slip_view')->name('slip_view');
            Route::post('invoices_detail_print', 'Admins\AdminFinanceController@invoices_detail_print')->name('invoices_detail_print');
            Route::post('print', 'Admins\AdminFinanceController@corporate_invoice_print')->name('invoices_print');
            Route::post('print_origin_wise', 'Admins\AdminFinanceController@invoices_print_origin_wise')->name('print_origin_wise');
            Route::post('print_gst_wise', 'Admins\AdminFinanceController@invoices_print_gst_wise')->name('print_gst_wise');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@invoices_export_to_excel')->name('export_to_excel');
            Route::put('email_reminder', 'Admins\AdminFinanceController@invoices_email_reminder')->name('email_reminder');
            Route::post('mark_as_received', 'Admins\AdminFinanceController@invoices_mark_as_received')->name('mark_as_received');
            Route::post('mark_as_received_all', 'Admins\AdminFinanceController@invoices_mark_as_received_all')->name('mark_as_received_all');
            Route::get('received', 'Admins\AdminFinanceController@received_invoices_index')->name('received_index');
            Route::get('received_list', 'Admins\AdminFinanceController@received_invoices_list')->name('received_list');
            Route::post('add_adjustment', 'Admins\AdminFinanceController@invoice_add_adjustment')->name('add_adjustment');
            Route::post('adjustment_view', 'Admins\AdminFinanceController@invoice_adjustment_view')->name('adjustment_view');

            //Route::get('download/{id}', 'Admins\AdminFinanceController@email_print_invoice')->name('download');

            Route::prefix('reimbursement')->name('reimbursement.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@reimbursement_invoices_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@reimbursement_invoices_list')->name('list');
                Route::post('print_origin_wise', 'Admins\AdminFinanceController@reimbursement_invoices_print_origin_wise')->name('print_origin_wise');
                Route::post('print_gst_wise', 'Admins\AdminFinanceController@reimbursement_invoices_print_gst_wise')->name('print_gst_wise');
                Route::get('export_to_excel', 'Admins\AdminFinanceController@reimbursement_invoices_export_to_excel')->name('export_to_excel');
                Route::post('detail_print', 'Admins\AdminFinanceController@reimbursement_detail_invoices_print')->name('detail_print');
                Route::post('print', 'Admins\AdminFinanceController@reimbursement_invoice_print')->name('invoices_print');
            });
        });

        Route::prefix('invoice_for_reimbursement')->name('invoice_for_reimbursement.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@invoice_for_reimbursement_index')->name('index');
            Route::get('generate', 'Admins\AdminFinanceController@invoice_for_reimbursement_generate')->name('generate');
        });
        Route::prefix('ftl_invoice')->name('ftl_invoice.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@ftl_invoice_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@ftl_invoice_list')->name('list');
            Route::post('received', 'Admins\AdminFinanceController@ftl_invoice_received')->name('received');
            Route::post('print', 'Admins\AdminFinanceController@ftl_invoice_print')->name('print');
            Route::get('export_to_excel', 'Admins\AdminFinanceController@ftl_invoice_export_to_excel')->name('export_to_excel');
        });

        Route::prefix('retail')->name('retail.')->group(function () {

            Route::prefix('make_payments')->name('make_payments.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@retail_make_payments_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@retail_make_payments_list')->name('list');
                Route::post('delivered_shipments', 'Admins\AdminFinanceController@retail_make_payments_delivered_shipments')->name('delivered_shipments');
                Route::post('adjusted_shipments', 'Admins\AdminFinanceController@retail_make_payments_adjusted_shipments')->name('adjusted_shipments');
                Route::post('shipment_details', 'Admins\AdminFinanceController@retail_make_payments_shipment_details')->name('shipment_details');
                Route::get('shipment_list', 'Admins\AdminFinanceController@retail_make_payments_shipment_list')->name('shipment_list');
                Route::get('shipment_export_selected', 'Admins\AdminFinanceController@retail_make_payments_shipment_export_selected')->name('shipment_export_selected');
                Route::post('verify', 'Admins\AdminFinanceController@retail_make_payments_verify')->name('verify');
                Route::get('export_bank_order', 'Admins\AdminFinanceController@retail_make_payments_export_bank_order')->name('export_bank_order');
                Route::post('store', 'Admins\AdminFinanceController@retail_make_payments_store')->name('store');
                Route::get('stats_calculate', 'Admins\AdminFinanceController@retail_make_payments_stats_calculate')->name('stats_calculate');
            });

            Route::prefix('done_payments')->name('done_payments.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@retail_done_payments_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@retail_done_payments_list')->name('list');
                Route::put('paid', 'Admins\AdminFinanceController@retail_done_payments_paid')->name('paid');
                Route::put('reverted', 'Admins\AdminFinanceController@retail_done_payments_reverted')->name('reverted');
                Route::post('delivered_shipments', 'Admins\AdminFinanceController@retail_done_payments_delivered_shipments')->name('delivered_shipments');
                Route::post('adjusted_shipments', 'Admins\AdminFinanceController@retail_done_payments_adjusted_shipments')->name('adjusted_shipments');
                Route::post('details_print', 'Admins\AdminFinanceController@retail_done_payments_details_print')->name('details_print');
                Route::post('details', 'Admins\AdminFinanceController@retail_done_payments_details')->name('details');
                Route::put('update_details', 'Admins\AdminFinanceController@retail_done_payments_update_details')->name('update_details');
                Route::get('export_to_excel', 'Admins\AdminFinanceController@retail_done_payments_export_to_excel')->name('export_to_excel');
                Route::post('excel_store', 'Admins\AdminFinanceController@retail_done_payments_excel_store')->name('excel_store');
                Route::get('retail_generate_report_to_email', 'Admins\AdminFinanceController@retail_done_payments_generate_report_to_email')->name('retail_generate_report_to_email');
            });
        });

        Route::prefix('tracking_number_wise_dncc_info')->name('tracking_number_wise_dncc_info.')->group(function () {
                Route::get('', 'Admins\AdminFinanceController@tracking_number_wise_dncc_info_index')->name('index');
                Route::get('list', 'Admins\AdminFinanceController@tracking_number_wise_dncc_info_list')->name('list');
        });

        Route::prefix('dncc_wise_tracking_number_info')->name('dncc_wise_tracking_number_info.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@dncc_wise_tracking_number_info_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@dncc_wise_tracking_number_info_list')->name('list');
        });

        Route::prefix('shipment_ledger')->name('shipment_ledger.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@service_charges_ledger_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@service_charges_ledger_list')->name('list');
        });

        Route::prefix('wallet_users')->name('wallet_users.')->group(function () {
            Route::get('', 'Admins\AdminFinanceController@wallet_user_index')->name('index');
            Route::get('list', 'Admins\AdminFinanceController@walle_user_list')->name('list');
        });
    });

    Route::prefix('petty_cash')->name('petty_cash.')->group(function () {
        Route::prefix('make')->name('make.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@make_petty_cash_statement_index')->name('index');
            Route::post('destination', 'Admins\AdminPettyCashController@make_petty_cash_statement_check_destination')->name('destination');
            Route::post('hubs', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_hubs')->name('hubs');
            Route::post('cities', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_cities')->name('cities');
            Route::post('dncc', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_dncc')->name('dncc');
            Route::post('employee', 'Admins\AdminPettyCashController@make_petty_cash_statement_get_employee')->name('employee');
            Route::post('reference', 'Admins\AdminPettyCashController@make_petty_cash_statement_check_reference')->name('reference');
            Route::post('titles', 'Admins\AdminPettyCashController@make_petty_cash_statement_titles')->name('titles');
            Route::post('submit', 'Admins\AdminPettyCashController@make_petty_cash_statement_submit')->name('submit');
        });
        Route::post('view/sdn_logs', 'Admins\AdminPettyCashController@sdn_log')->name('sdn_logs');
        Route::prefix('statements')->name('statements.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@petty_cash_statements_list')->name('list');
            Route::post('print', 'Admins\AdminPettyCashController@statement_print')->name('print');
            Route::post('approve', 'Admins\AdminPettyCashController@petty_cash_statements_approve')->name('approve');
            Route::post('reject_all', 'Admins\AdminPettyCashController@petty_cash_statements_reject_all')->name('reject_all');
            Route::get('{id}/edit', 'Admins\AdminPettyCashController@edit_petty_cash_statement_index')->name('edit');
            Route::get('{id}/edit/list', 'Admins\AdminPettyCashController@edit_petty_cash_statement_list')->name('edit.list');
            Route::post('edit/approve', 'Admins\AdminPettyCashController@edit_petty_cash_statements_approve')->name('edit.approve');
            Route::post('edit/reject', 'Admins\AdminPettyCashController@edit_petty_cash_statements_reject')->name('edit.reject');
            Route::put('edit/submit', 'Admins\AdminPettyCashController@edit_petty_cash_statements_submit')->name('edit.submit');
            Route::post('view/amount', 'Admins\AdminPettyCashController@edit_petty_cash_statements_amount')->name('view.amount');
            Route::get('reference_document/{reference_document}', 'Admins\AdminPettyCashController@reference_document')->name('reference_document');
            Route::post('station_operation_finance_approved', 'Admins\AdminPettyCashController@petty_cash_station_operation_finance_approved_all')->name('station_operation_finance_approved');
            Route::post('check', 'Admins\AdminPettyCashController@petty_cash_statement_check')->name('check');
        });
        Route::prefix('approved')->name('approved.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@approved_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@approved_petty_cash_statements_list')->name('list');
            Route::get('{id}/view', 'Admins\AdminPettyCashController@approved_petty_cash_statements_view')->name('view');
            Route::get('{id}/view/list', 'Admins\AdminPettyCashController@approved_petty_cash_statements_view_list')->name('view.list');
            Route::post('paid', 'Admins\AdminPettyCashController@approved_petty_cash_statements_paid')->name('paid');
            Route::post('adjusted', 'Admins\AdminPettyCashController@approved_petty_cash_statements_adjusted')->name('adjusted');
            Route::post('bulk_adjusted', 'Admins\AdminPettyCashController@approved_petty_cash_statements_bulk_adjusted')->name('bulk_adjusted');
        });
        Route::prefix('rejected')->name('rejected.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@rejected_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@rejected_petty_cash_statements_list')->name('list');
        });
        Route::prefix('draft')->name('draft.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@draft_petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminPettyCashController@draft_petty_cash_statements_list')->name('list');
            Route::get('{id}/edit', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statement_index')->name('edit');
            Route::get('{id}/edit/list', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statement_list')->name('edit.list');
            Route::put('edit/submit', 'Admins\AdminPettyCashController@draft_edit_petty_cash_statements_submit')->name('edit.submit');
            Route::get('reference_document/{reference_document}', 'Admins\AdminPettyCashController@draft_reference_document')->name('reference_document');
        });
        Route::prefix('edit')->name('edit.')->group(function () {
            Route::post('edit_petty_cash', 'Admins\AdminPettyCashController@edit_petty_cash')->name('edit_petty_cash');
            Route::post('edit_petty_cash_amount', 'Admins\AdminPettyCashController@edit_petty_cash_amount')->name('edit_petty_cash_amount');
        });

        Route::prefix('advance')->name('advance.')->group(function () {
            Route::get('', 'Admins\AdminPettyCashController@advance_petty_cash_index')->name('index'); //todo new
            Route::post('submit', 'Admins\AdminPettyCashController@advance_petty_cash_submit')->name('submit'); //todo new
            Route::post('check/reference', 'Admins\AdminPettyCashController@advance_petty_cash_statement_check_reference')->name('reference'); //todo new

            Route::prefix('statements')->name('statements.')->group(function () {
                Route::get('', 'Admins\AdminPettyCashController@advance_petty_cash_statements_index')->name('index'); //todo new
                Route::get('list', 'Admins\AdminPettyCashController@advance_petty_cash_statements_list')->name('list'); //todo new
                Route::post('advance_print', 'Admins\AdminPettyCashController@advance_statement_print')->name('advance_print'); //todo new
                Route::post('advance_view/sdn_logs', 'Admins\AdminPettyCashController@advance_sdn_log')->name('advance_sdn_logs'); //todo new

                //make details:
                Route::get('{id}/make_detail', 'Admins\AdminPettyCashController@advance_add_petty_cash_statement_make_detail')->name('make_detail'); //todo new
                Route::post('make_detail_submit', 'Admins\AdminPettyCashController@advance_make_petty_cash_statement_detail_submit')->name('make_detail_submit'); //todo new

                Route::get('{id}/edit_make_detail', 'Admins\AdminPettyCashController@advance_edit_petty_cash_statement_make_detail')->name('edit_make_detail'); //todo new
                Route::get('{id}/edit_make_detail/list', 'Admins\AdminPettyCashController@advance_edit_petty_cash_statement_make_detail_list')->name('edit_make_detail.list'); //todo new
                Route::post('edit_make_detail_submit', 'Admins\AdminPettyCashController@advance_edit_make_petty_cash_statement_detail_submit')->name('edit_make_detail_submit'); //todo new

                Route::post('detail_edit/approve', 'Admins\AdminPettyCashController@detail_edit_petty_cash_statements_approve')->name('detail_edit.approve'); //todo new
                Route::post('detail_edit/reject', 'Admins\AdminPettyCashController@detail_edit_petty_cash_statements_reject')->name('detail_edit.reject'); //todo new
            });
        });

    });

    Route::prefix('month_closing')->name('month_closing.')->group(function () {
        Route::get('', 'Admins\AdminMonthClosingController@month_closing_index')->name('index');
        Route::get('list', 'Admins\AdminMonthClosingController@month_closing_list')->name('list');
        Route::post('add', 'Admins\AdminMonthClosingController@add_shipment')->name('add');
        Route::post('confirm', 'Admins\AdminMonthClosingController@return_confirm_shipment')->name('confirm');
        Route::post('reattempt', 'Admins\AdminMonthClosingController@return_reattempt_shipment')->name('reattempt');


        Route::prefix('pending')->name('pending.')->group(function () {
            Route::get('', 'Admins\AdminMonthClosingController@pending_index')->name('index');
            Route::get('list', 'Admins\AdminMonthClosingController@pending_list')->name('list');
            Route::post('assign', 'Admins\AdminMonthClosingController@assign_responsible_submit')->name('assign');
            Route::post('closing_type_update', 'Admins\AdminMonthClosingController@closing_status_submit')->name('closing_type_update');
            Route::post('resolved', 'Admins\AdminMonthClosingController@month_closing_resolved')->name('resolved');

            Route::post('assign_details', 'Admins\AdminMonthClosingController@edit_assign_details')->name('assign_details');
            Route::post('assign_update', 'Admins\AdminMonthClosingController@assign_responsible_update')->name('assign_update');
        });

        Route::prefix('resolved')->name('resolved.')->group(function () {
            Route::get('', 'Admins\AdminMonthClosingController@resolved_index')->name('index');
            Route::get('list', 'Admins\AdminMonthClosingController@resolved_list')->name('list');
            Route::post('closed', 'Admins\AdminMonthClosingController@month_closing_closed')->name('closed');
        });
    });


    Route::prefix('sameday')->name('sameday.')->group(function () {
        Route::get('', 'Admins\SamedayController@sameday_index')->name('index');
        Route::get('list', 'Admins\SamedayController@sameday_list')->name('list');
    });
    Route::prefix('packaging')->name('packaging.')->group(function () {
        Route::get('', 'Admins\AdminPackagingMaterialController@packaging_index')->name('index');
        Route::get('list', 'Admins\AdminPackagingMaterialController@packaging_list')->name('list');
        Route::post('add/submit', 'Admins\AdminPackagingMaterialController@add_stock')->name('add.submit');
        Route::get('fetch/cities', 'Admins\AdminPackagingMaterialController@fetch_cities')->name('fetch.cities');
        Route::post('send/submit', 'Admins\AdminPackagingMaterialController@send_stock')->name('send.submit');
        Route::prefix('requests')->name('requests.')->group(function () {
            Route::get('', 'Admins\AdminPackagingMaterialController@request_index')->name('index');
            Route::get('list', 'Admins\AdminPackagingMaterialController@request_list')->name('list');
            Route::put('', 'Admins\AdminPackagingMaterialController@request_update')->name('update');
            Route::post('submit', 'Admins\AdminPackagingMaterialController@request_submit')->name('submit');
            Route::post('check_quantity', 'Admins\AdminPackagingMaterialController@request_check_quantity')->name('check_quantity');
            Route::post('dispatch', 'Admins\AdminPackagingMaterialController@request_dispatch_submit')->name('dispatch');
            Route::post('quantity_details', 'Admins\AdminPackagingMaterialController@quantity_details')->name('quantity_details');
            Route::post('confirm', 'Admins\AdminPackagingMaterialController@request_confirm')->name('confirm');
            Route::post('cancel', 'Admins\AdminPackagingMaterialController@request_cancel')->name('cancel');
            Route::post('replenish', 'Admins\AdminPackagingMaterialController@request_replenish')->name('replenish');
            Route::post('completed', 'Admins\AdminPackagingMaterialController@request_completed')->name('completed');
            Route::post('good_receiving_note', 'Admins\AdminPackagingMaterialController@good_receiving_note')->name('good_receiving_note');
            Route::post('sizes', 'Admins\AdminPackagingMaterialController@packaging_request_sizes')->name('sizes');
            Route::post('remarks', 'Admins\AdminPackagingMaterialController@packaging_request_remarks')->name('remarks');
            Route::get('pickup_address', 'Admins\AdminPackagingMaterialController@fetch_pickup_address')->name('pickup_address');
            Route::post('submit', 'Admins\AdminPackagingMaterialController@packaging_request_submit')->name('submit');
        });
        Route::prefix('types')->name('types.')->group(function () {
            Route::get('', 'Admins\AdminPackagingMaterialController@types_index')->name('index');
            Route::get('list', 'Admins\AdminPackagingMaterialController@types_list')->name('list');
            Route::post('add', 'Admins\AdminPackagingMaterialController@type_add')->name('add');
            Route::get('all_shippers', 'Admins\AdminPackagingMaterialController@all_shippers')->name('all_shippers');
            Route::get('all_shippers_edit', 'Admins\AdminPackagingMaterialController@all_shippers_edit')->name('all_shippers_edit');

            Route::post('details', 'Admins\AdminPackagingMaterialController@type_details')->name('details');
            Route::post('edit', 'Admins\AdminPackagingMaterialController@type_edit')->name('edit');
            Route::post('enable_disable', 'Admins\AdminPackagingMaterialController@type_enable_disable')->name('enable_disable');
        });
        Route::prefix('warehouse')->name('warehouse.')->group(function () {
            Route::get('', 'Admins\AdminPackagingMaterialController@warehouse_index')->name('index');
            Route::get('list', 'Admins\AdminPackagingMaterialController@warehouse_list')->name('list');
            Route::post('enable_disable', 'Admins\AdminPackagingMaterialController@warehouse_enable_disable')->name('enable_disable');
            Route::post('add', 'Admins\AdminPackagingMaterialController@warehouse_add')->name('add');
            Route::post('edit_data', 'Admins\AdminPackagingMaterialController@warehouse_edit_data')->name('edit_data');
            Route::post('edit', 'Admins\AdminPackagingMaterialController@warehouse_edit')->name('edit');
            Route::post('master_add', 'Admins\AdminPackagingMaterialController@warehouse_master_add')->name('master_add');
            Route::post('warehouse_hubs', 'Admins\AdminPackagingMaterialController@warehouse_hubs')->name('warehouse_hubs');
        });
        Route::prefix('stock_request')->name('stock_request.')->group(function () {
            Route::post('cancel', 'Admins\AdminPackagingMaterialController@stock_request_cancel')->name('cancel');
            Route::post('confirm', 'Admins\AdminPackagingMaterialController@stock_request_confirm')->name('confirm');
            Route::post('details', 'Admins\AdminPackagingMaterialController@stock_request_details')->name('details');
            Route::post('dispatch', 'Admins\AdminPackagingMaterialController@stock_request_dispatch')->name('dispatch');
        });
        Route::prefix('stock_send')->name('stock_send.')->group(function () {
            Route::post('submit', 'Admins\AdminPackagingMaterialController@stock_send_submit')->name('submit');
        });

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('', 'Admins\AdminPackagingMaterialController@inventory_index')->name('index');
            Route::post('list', 'Admins\AdminPackagingMaterialController@inventory_list')->name('list');
        });
    });

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@index')->name('index');
        Route::get('list', 'Admins\AdminNotificationsController@list')->name('list');
        Route::post('send_custom_email', 'Admins\AdminNotificationsController@send_custom_email')->name('send_custom_email');
        Route::post('details', 'Admins\AdminNotificationsController@details')->name('details');
        Route::post('status', 'Admins\AdminNotificationsController@status')->name('status');
        Route::post('edit', 'Admins\AdminNotificationsController@edit')->name('edit');
        Route::post('send_custom_notification', 'Admins\AdminNotificationsController@send_custom_notification')->name('send_custom_notification');
    });

    Route::prefix('app_notifications')->name('app_notifications.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@app_notification_index')->name('index');
        Route::get('list', 'Admins\AdminNotificationsController@app_notification_list')->name('list');
        Route::post('status', 'Admins\AdminNotificationsController@app_notification_status')->name('status');
        Route::post('details', 'Admins\AdminNotificationsController@app_notification_details')->name('details');
        Route::post('edit', 'Admins\AdminNotificationsController@app_notification_edit')->name('edit');
    });

    // SMS logs
    Route::prefix('sms_logs')->name('sms_logs.')->group(function () {
        Route::get('', 'Admins\AdminNotificationsController@sms_logs_view')->name('index');
        Route::post('list', 'Admins\AdminNotificationsController@sms_logs')->name('list');
    });

    //Reports start
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('data_for_dropdown/{type}','Admins\AdminDashboardController@data_for_dropdown')->name('data_for_dropdown');
        Route::prefix('qsr')->name('qsr.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@qsr_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@qsr_list')->name('list');
            Route::get('updated_shippers_list', 'Admins\AdminReportsController@updated_shippers_list')->name('updated_shippers_list');
        });

        Route::prefix('wht')->name('wht.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@wht_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@wht_list')->name('list');
        });
        Route::prefix('kam_and_poc_qsr')->name('kam_and_poc_qsr.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@kam_and_poc_qsr_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@kam_and_poc_qsr_list')->name('list');
        });

        Route::prefix('qsr_old')->name('qsr_old.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@qsrold_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@qsrold_list')->name('list');
        });
        Route::prefix('return_note')->name('return_note.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@return_note_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_note_list')->name('list');
            Route::post('delivered_shipments', 'Admins\AdminReportsController@history_delivered_shipments')->name('delivered_shipments');
            Route::post('shipments', 'Admins\AdminReportsController@return_note_shipments')->name('shipments');
        });
        Route::prefix('pickup_note')->name('pickup_note.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@pickup_note_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pickup_note_list')->name('list');
            Route::post('bookings', 'Admins\AdminReportsController@pickup_note_bookings')->name('bookings');
        });
        Route::prefix('cargo_received')->name('cargo_received.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@cargo_received_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_received_list')->name('list');
            Route::post('shipments', 'Admins\AdminReportsController@cargo_shipments')->name('shipments');
        });
        Route::prefix('multiple_iban')->name('multiple_iban.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@multiple_iban_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@multiple_iban_list')->name('list');
        });
        Route::prefix('completed_aging')->name('completed_aging.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@completed_aging_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@completed_aging_list')->name('list');
        });
        Route::prefix('pending_cash_collection')->name('pending_cash_collection.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@pending_cash_collection_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pending_cash_collection_list')->name('list');
        });
        Route::prefix('lead_time')->name('lead_time.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@lead_time_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@lead_time_list')->name('list');
        });
        Route::prefix('qa')->name('qa.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@qa_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@qa_list')->name('list');
        });
        Route::prefix('outstanding_shipments')->name('outstanding_shipments.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@outstanding_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@outstanding_shipments_list')->name('list');
        });
        Route::prefix('daily_pickup_sales')->name('daily_pickup_sales.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@daily_pickup_sales_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@daily_pickup_sales_export_to_excel')->name('export_to_excel');
            Route::post('download', 'Admins\AdminReportsController@daily_pickup_sales_download')->name('download');
        });
        Route::prefix('customer_sales')->name('customer_sales.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@customer_sales_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_sales_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_sales_download')->name('download');
        });
        Route::prefix('completed_delivery_notes')->name('completed_delivery_notes.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@completed_delivery_notes_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@completed_delivery_notes_list')->name('list');
            Route::post('shipments', 'Admins\AdminReportsController@completed_shipments')->name('shipments');
            Route::post('shipments/delivered', 'Admins\AdminReportsController@completed_shipments_delivered')->name('shipments.delivered');
        });
        Route::prefix('customer_retention')->name('customer_retention.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@customer_retention_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@customer_retention_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@customer_retention_download')->name('download');
        });
        Route::prefix('overall_sales')->name('overall_sales.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@overall_sales_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@overall_sales_list')->name('list');
        });
        Route::prefix('petty_cash')->name('petty_cash.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@petty_cash_statements_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@petty_cash_statements_list')->name('list');
        });
        Route::prefix('negative_balance_customers')->name('negative_balance_customers.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@negative_balance_customers_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@negative_balance_customers_list')->name('list');
        });
        Route::prefix('call_verification')->name('call_verification.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@call_verification_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@call_verification_list')->name('list');
        });
        Route::prefix('sales_person_performance')->name('sales_person_performance.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sales_person_performance_index')->name('index');
            Route::post('export_to_excel', 'Admins\AdminReportsController@sales_person_performance_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Admins\AdminReportsController@sales_person_performance_download')->name('download');
        });
        Route::prefix('rider_unresponsive_report')->name('rider_unresponsive_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rider_unresponsive_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@rider_unresponsive_report_list')->name('list');
        });

        Route::prefix('fake_status')->name('fake_status.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@fake_status_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@fake_status_list')->name('list');
            Route::post('shipments/total', 'Admins\AdminReportsController@fake_status_shipments_total')->name('shipments.total');
            Route::post('shipments/undelivered', 'Admins\AdminReportsController@fake_status_shipments_undelivered')->name('shipments.undelivered');
            Route::post('fake_status_shipment', 'Admins\AdminReportsController@fake_status_shipments')->name('fake_status_shipment');
        });

        Route::prefix('debriefing')->name('debriefing.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@debriefing_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@debriefing_list')->name('list');
            Route::get('agent-report', 'Admins\AdminReportsController@debriefing_agent_report')->name('agent_index');
            Route::get('agent-report/list', 'Admins\AdminReportsController@debriefing_agent_report_list')->name('agent_list');
            Route::get('export', 'Admins\AdminReportsController@debriefing_export')->name('export');
        });
        Route::prefix('cargo_returns_shipment')->name('cargo_returns_shipment.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@cargo_returns_shipment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_returns_shipment_list')->name('list');
        });
        Route::prefix('return_reattempt_ratio')->name('return_reattempt_ratio.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@return_reattempt_ratio_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_reattempt_ratio_list')->name('list');
        });
        Route::prefix('multiple_payment_report')->name('multiple_payment_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@multiple_payment_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@multiple_payment_report_list')->name('list');
        });
        Route::prefix('revenue')->name('revenue.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@revenue_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@revenue_list')->name('list');
        });
        Route::prefix('crm')->name('crm.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@crm_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_list')->name('list');
        });
        Route::prefix('gst')->name('gst.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@gst_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@gst_list')->name('list');
        });

        Route::prefix('summary')->name('summary.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@summary_index')->name('index');
            Route::post('data', 'Admins\AdminReportsController@summary_data')->name('data');
            Route::get('list', 'Admins\AdminReportsController@summary_list')->name('list');
        });
        Route::prefix('account_activation')->name('account_activation.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@account_activation_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@account_activation_list')->name('list');
        });
        Route::prefix('adjustments')->name('adjustments.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@adjustments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@adjustments_list')->name('list');
        });

        Route::prefix('sdn')->name('sdn.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sdn_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sdn_list')->name('list');
        });
        Route::prefix('account_edit')->name('account_edit.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@account_edit_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@account_edit_list')->name('list');
        });
        Route::prefix('bank_history')->name('bank_history.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@bank_history_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@bank_history_list')->name('list');
        });

        Route::prefix('consignee_details')->name('consignee_details.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@consignee_details_history_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@consignee_details_history_list')->name('list');
        });
        Route::prefix('booked_and_cancelled')->name('booked_and_cancelled.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@booked_and_cancelled_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@booked_and_cancelled_list')->name('list');
        });

        Route::prefix('fake_status_shipments')->name('fake_status_shipments.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@fake_status_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@fake_status_shipments_list')->name('list');
        });
        Route::prefix('daily_visit')->name('daily_visit.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@daily_visit_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@daily_visit_list')->name('list');
            Route::post('set_visit_status', 'Admins\AdminReportsController@set_visit_status')->name('set_visit_status');
        });
        Route::prefix('delivered_shipment')->name('delivered_shipment.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@delivered_shipment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@delivered_shipment_list')->name('list');
        });
        Route::prefix('route_distribution')->name('route_distribution.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@route_distribution_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@route_distribution_list')->name('list');
        });
        Route::prefix('destination_delivery_received')->name('destination_delivery_received.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@destination_delivery_received_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@destination_delivery_received_list')->name('list');
        });

        Route::prefix('account_reconciliation')->name('account_reconciliation.')->group(function () {
            Route::get('', 'Reports\AccountReconciliationController@account_reconciliation_index')->name('index');
            Route::post('export_to_excel', 'Reports\AccountReconciliationController@account_reconciliation_export_to_excel')->name('export_to_excel');
            Route::get('download', 'Reports\AccountReconciliationController@account_reconciliation_download')->name('download');
        });
        Route::prefix('cargo_short_received_shipments')->name('cargo_short_received_shipments.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@cargo_short_received_shipments_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@cargo_short_received_shipments_list')->name('list');
        });

        Route::prefix('last_mile_status')->name('last_mile_status.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@last_mile_status_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@last_mile_status_list')->name('list');
        });

        Route::prefix('pickup_report')->name('pickup_report.')->group(function () {
            Route::get('', 'Admins\V2Pickup\V2AdminReportController@pickup_report_index')->name('index');
            Route::get('list', 'Admins\V2Pickup\V2AdminReportController@pickup_report_list')->name('list');
            Route::post('/data', 'Admins\V2Pickup\V2AdminReportController@pickup_report_data')->name('data');
        });

        Route::prefix('not_attempted_aging')->name('not_attempted_aging.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@not_attempted_aging_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@not_attempted_aging_list')->name('list');
        });
        Route::prefix('station_recovery')->name('station_recovery.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@station_recovery_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@station_recovery_list')->name('list');
            Route::post('update', 'Admins\AdminReportsController@station_recovery_update')->name('update');
        });
        Route::prefix('daily_monthly_adjustment')->name('daily_monthly_adjustment.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@daily_monthly_adjustment_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@daily_monthly_adjustment_list')->name('list');
            Route::get('summary_list', 'Admins\AdminReportsController@daily_monthly_adjustment_summary_list')->name('summary_list');
        });
        Route::prefix('petty_cash_expense_summary')->name('petty_cash_expense_summary.')->group(function () {
            Route::get('', 'Admins\PettyCashExpenseSummaryReport@index')->name('index');
            Route::get('petty_cash_summary_report', 'Admins\PettyCashExpenseSummaryReport@pettyCashSummaryReportProcess')->name('petty_cash_summary_report');
        });
        Route::prefix('app_efficiency')->name('app_efficiency.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@app_efficiency_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@app_efficiency_list')->name('app_efficiency_list');
        });
        Route::prefix('sales_incentive')->name('sales_incentive.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sales_incentive_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sales_incentive_list')->name('list');
            Route::get('consolidated', 'Admins\AdminReportsController@consolidated_sales_incentive_index')->name('consolidated');
            Route::get('consolidated_list', 'Admins\AdminReportsController@consolidated_sales_incentive_list')->name('consolidated_list');
        });
        Route::prefix('month_closing')->name('month_closing.')->group(function () {
            Route::prefix('individual')->name('individual.')->group(function () {
                Route::get('', 'Admins\AdminMonthClosingReportsController@month_closing_individual_index')->name('index');
                Route::get('list', 'Admins\AdminMonthClosingReportsController@month_closing_individual_list')->name('list');
            });
            Route::prefix('pivot')->name('pivot.')->group(function () {
                Route::get('', 'Admins\AdminMonthClosingReportsController@month_closing_pivot_index')->name('index');
                Route::get('list', 'Admins\AdminMonthClosingReportsController@month_closing_pivot_list')->name('list');
            });
        });
        Route::prefix('osa_charges')->name('osa_charges.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@osa_charges_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@osa_charges_list')->name('list');
        });

        Route::prefix('last_mile_app')->name('last_mile_app.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@last_mile_app_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@last_mile_app_list')->name('list');
            Route::post('shipment_list', 'Admins\AdminReportsController@shipment_list')->name('shipment_list');
            Route::get('app_shipments_list', 'Admins\AdminReportsController@last_mile_app_shipments_list')->name('app_shipments_list');
            Route::get('dbf_shipments_list', 'Admins\AdminReportsController@last_mile_dbf_shipments_list')->name('dbf_shipments_list');
        });
        Route::prefix('weight_qc')->name('weight_qc.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@weight_qc_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@weight_qc_list')->name('list');
        });
        Route::prefix('master_cargo')->name('master_cargo.')->group(function () {
            Route::prefix('bag')->name('bag.')->group(function () {
                Route::prefix('in_transit')->name('in_transit.')->group(function () {
                    Route::get('', 'Admins\AdminReportsController@in_transit_index')->name('index');
                    Route::get('list', 'Admins\AdminReportsController@in_transit_list')->name('list');
                    Route::post('shipments', 'Admins\AdminReportsController@in_transit_shipments')->name('shipments');
                    Route::post('short_received', 'Admins\AdminReportsController@short_received_shipments')->name('short_received');
                });
            });
            Route::prefix('short_received_shipments')->name('short_received_shipments.')->group(function () {
                Route::get('', 'Admins\AdminReportsController@master_cargo_short_received_shipments_index')->name('index');
                Route::get('list', 'Admins\AdminReportsController@master_cargo_short_received_shipments_list')->name('list');
            });
        });

        Route::prefix('retail_sales')->name('retail_sales.')->group(function () {
            Route::get('', 'Admins\AdminRetailReportController@sales_index')->name('index');
            Route::post('list', 'Admins\AdminRetailReportController@sales_list')->name('list');
        });

        Route::prefix('shipper_insurance')->name('shipper_insurance.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@shipper_insurance_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@shipper_insurance_list')->name('list');
            Route::post('charges', 'Admins\AdminReportsController@shipper_insurance_charges')->name('charges');
        });

        Route::prefix('operation_service_level')->name('operation_service_level.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@operation_service_level_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@operation_service_level_list')->name('list');
        });
        Route::prefix('work_code_master')->name('work_code_master.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@work_code_master_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@work_code_master_list')->name('list');
        });

        Route::prefix('manifest')->name('manifest.')->group(function () {
            Route::prefix('short_received_shipments')->name('short_received_shipments.')->group(function () {
                Route::get('', 'Admins\AdminReportsController@manifest_short_received_shipments_index')->name('index');
                Route::get('list', 'Admins\AdminReportsController@manifest_short_received_shipments_list')->name('list');
            });
        });

        Route::prefix('reverse_pickup')->name('reverse_pickup.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@reverse_pickup_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@reverse_pickup_list')->name('list');
        });
        Route::prefix('dws_report')->name('dws_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@dws_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@dws_report_list')->name('list');
        });
        Route::prefix('revert')->name('revert.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@return_revert_log')->name('index');
            Route::get('list', 'Admins\AdminReportsController@return_revert_list')->name('list');
        });

        Route::prefix('pickup_history_cn_wise')->name('pickup_history_cn_wise.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@pickup_history_cn_wise_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@pickup_history_cn_wise_list')->name('list');
        });
        Route::prefix('crm_count')->name('crm_count.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@crm_count_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_count_list')->name('list');
        });
        Route::prefix('crm_agent_wise_report')->name('crm_agent_wise_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@crm_agent_wise_report_index')->name('index');
            Route::post('crm_agent_wise_report_list', 'Admins\AdminReportsController@crm_agent_wise_report_list')->name('list');
        });
        Route::prefix('crm_special_approval')->name('crm_special_approval.')->group(function () {

            Route::get('', 'Admins\AdminReportsController@crm_special_approval_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@crm_special_approval_list')->name('list');
            Route::get('get_approvers', 'Admins\AdminReportsController@get_approvers')->name('get_approvers');
        });

        Route::prefix('mms')->name('mms.')->group(function () {
            Route::get('', 'Admins\Reports\MMSReportController@index')->name('index');
            Route::post('list', 'Admins\Reports\MMSReportController@list')->name('list');
        });
        Route::prefix('logistic')->name('logistic.')->group(function () {
            Route::get('', 'Admins\Reports\LogisticReportController@index')->name('index');
            Route::post('list', 'Admins\Reports\LogisticReportController@list')->name('list');
        });
        Route::prefix('employee_confirmation')->name('employee_confirmation.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@employee_confirmation_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@employee_confirmation_list')->name('list');
        });

        Route::prefix('ssr')->name('ssr.')->group(function () {
            Route::get('', 'Admins\Reports\SSRController@ssr_index')->name('index');
            Route::post('list', 'Admins\Reports\SSRController@ssr_list')->name('list');
            Route::post('get_sub_segments','Admins\Reports\SSRController@get_sub_segments')->name('get_sub_segments');
        });

        Route::prefix('shipper_summary')->name('shipper_summary.')->group(function () {
            Route::get('', 'Admins\Reports\ShipperSummaryReportController@index')->name('index');
            Route::get('list', 'Admins\Reports\ShipperSummaryReportController@list')->name('list');
        });

        Route::prefix('one_link_charges_summary')->name('one_link_charges_summary.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@one_link_charges_summary_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@one_link_charges_summary_list')->name('list');
        });


        Route::prefix('hbl_konnect')->name('hbl_konnect.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@hbl_konnect_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@hbl_konnect_list')->name('list');
        });

        Route::prefix('pay_fast')->name('pay_fast_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@pay_fast_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@pay_fast_report_list')->name('list');
        });

        Route::prefix('csat')->name('csat_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@csat_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@csat_report_list')->name('list');
        });
        Route::prefix('rider_pickup')->name('rider_pickup.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rider_pickup_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@rider_pickup_list')->name('list');
            Route::post('scanned_shipments', 'Admins\AdminReportsController@rider_pickup_scanned_shipments')->name('scanned_shipments');
            Route::post('arrived_shipments', 'Admins\AdminReportsController@rider_pickup_arrived_shipments')->name('arrived_shipments');
            Route::post('without_scan_shipments', 'Admins\AdminReportsController@rider_pickup_without_scan_shipments')->name('without_scan_shipments');

        });
        Route::prefix('revenue_report_by_invoice')->name('revenue_report_by_invoice.')->group(function () {
            Route::get('', 'Admins\AdminRevenueReportsController@revenue_report_by_invoice_index')->name('index');
            Route::post('list', 'Admins\AdminRevenueReportsController@revenue_report_by_invoice_list')->name('list');
        });
        Route::prefix('project_arrival')->name('project_arrival.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@project_arrival_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@project_arrival_list')->name('list');
        });
        Route::prefix('rider_picked')->name('rider_picked.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rider_picked_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@rider_picked_list')->name('list');
            Route::post('scanned_shipments', 'Admins\AdminReportsController@rider_picked_arrival_scanned_shipments')->name('scanned_shipments');
            Route::post('arrived_shipments', 'Admins\AdminReportsController@rider_picked_arrival_arrived_shipments')->name('arrived_shipments');
        });
        Route::prefix('quick_scanned_report')->name('quick_scanned_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@quick_scanned_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@quick_scanned_report_list')->name('list');
        });

        Route::prefix('overland')->name('overland.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@overland_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@overland_list')->name('list');
        });

        Route::prefix('operations_performance')->name('operations_performance.')->group(function () {
            Route::get('data', 'Admins\AdminReportsController@ajax_load_operation_data')->name('data');
            Route::get('', 'Admins\AdminReportsController@operations_performance_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@operations_performance_export_to_excel')->name('export_to_excel');
        });


        Route::prefix('fintech')->name('fintech_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@fintech_report_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@fintech_report_list')->name('list');
        });

        Route::prefix('rv_report')->name('rv_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rv_report_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@rv_report_list')->name('list');
            Route::get('rv_call_history', 'Admins\AdminReportsController@rv_call_history')->name('rv_call_history');
            });

        Route::prefix('rvr_call_history')->name('rvr_call_history.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rvr_call_history_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@rvr_call_history_list')->name('list');
        });
        
        Route::prefix('rvr_reattempt')->name('rvr_reattempt.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@reattemptRvReport')->name('index');
            Route::post('list', 'Admins\AdminReportsController@rvReattemptList')->name('list');
        });

        Route::prefix('bot_rvr')->name('bot_rvr.')->group(function () {
            Route::get('', 'Admins\AdminRvReportsController@botRvCallRecord')->name('index');
            Route::post('list', 'Admins\AdminRvReportsController@botRvCallRecordList')->name('list');
        });
        
        Route::prefix('bot_rvr_log')->name('bot_rvr_log.')->group(function () {
            Route::get('', 'Admins\AdminRvReportsController@botRvCallLogs')->name('index');
            Route::post('list', 'Admins\AdminRvReportsController@botRvCallLogsList')->name('list');
        });

        Route::prefix('operation_disorder_report')->name('operation_disorder_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@ordinary_discrepancy_report_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@ordinary_discrepancy_report_list')->name('list');
            Route::post('tracking_data', 'Admins\AdminReportsController@ordinary_discrepancy_report_tracking_data')->name('tracking_data');
            Route::post('submit_tracking', 'Admins\AdminReportsController@submit_tracking')->name('submit_tracking');
            Route::get('list_for_tracking_screen', 'Admins\AdminReportsController@list_for_tracking_screen')->name('list_for_tracking_screen');

        });

        Route::prefix('ibft_report')->name('ibft_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@ibft_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@ibft_report_list')->name('list');
        });
		Route::prefix('rv_action_count_report')->name('rv_action_count_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@rv_action_count_report_index')->name('index');
            Route::post('', 'Admins\AdminReportsController@fetch_rv_action_count_report')->name('fetch');
        });
        Route::prefix('sack_bag_utilization')->name('sack_bag_utilization.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sack_bag_utilization_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sack_bag_utilization_list')->name('list');
            Route::post('sack_bag_list', 'Admins\AdminReportsController@get_sack_bag_list')->name('sack_bag_list');
        });
        Route::prefix('sack_bag_status')->name('sack_bag_status.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sack_bag_status_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@sack_bag_status_list')->name('list');
        });
        // Route::prefix('reused_sack_bag')->name('reused_sack_bag.')->group(function () {
        //     Route::get('', 'Admins\AdminReportsController@reused_sack_bag_index')->name('index');
        //     Route::get('list', 'Admins\AdminReportsController@reused_sack_bag_list')->name('list');
        // });
        Route::prefix('issuance_sack_bag')->name('issuance_sack_bag.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@issuance_sack_bag_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@issuance_sack_bag_list')->name('list');
            Route::post('sack_bag_list', 'Admins\AdminReportsController@get_issuance_sack_bag_list')->name('sack_bag_list');
        });

        Route::prefix('cargo_manifest')->name('cargo_manifest.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@cargo_manifest_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@cargo_manifest_list')->name('list');
        });

        Route::prefix('sms')->name('sms.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@sms_report_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@sms_report_list')->name('list');
        });
        Route::prefix('ops')->name('ops_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@ops_report_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@ops_report_list')->name('list');
        });

        Route::prefix('shipment_reversal')->name('shipment_reversal_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@shipment_reversal_index')->name('index');
            Route::get('list', 'Admins\AdminReportsController@shipment_reversal_list')->name('list');
        });
        
        Route::prefix('lost_and_case_closed_summary')->name('lost_and_case_closed_summary_report.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@lost_and_case_closed_summary_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@lost_and_case_closed_summary_list')->name('list');
        });

        // KAM AND POC QRS REPORT
        Route::prefix('kam_and_poc_qsr')->name('kam_and_poc_qsr.')->group(function () {
            Route::get('', 'Admins\AdminReportsController@kam_and_poc_qsr_index')->name('index');
            Route::post('list', 'Admins\AdminReportsController@kam_and_poc_qsr_list')->name('list');
        });
    });

    //Reports end

    Route::prefix('cancelled_shipments')->name('cancelled_shipments.')->group(function () {

        Route::get('', 'Admins\AdminShipmentCancelController@index')->name('index');
        Route::get('list', 'Admins\AdminShipmentCancelController@list')->name('list');
        Route::put('bulk_revert', 'Admins\AdminShipmentCancelController@bulk_revert')->name('bulk_revert');
        Route::put('revert', 'Admins\AdminShipmentCancelController@revert')->name('revert');
    });

    Route::get('/logout', 'Auth\AdminLoginController@logout')->name('logout');
    Route::post('/logout', 'Auth\AdminLoginController@logout')->name('logout');
    Route::get('/accounts/pending/{id}/bank', 'Admins\AdminDashboardController@viewBankInfo');
    Route::get('/accounts/pending/{id}/shipping', 'Admins\AdminDashboardController@viewShippingInfo');
    Route::get('/accounts/pending/{id}/rates', 'Admins\AdminDashboardController@viewShipperRates');
    //Reset Password
    Route::post('password/email', 'Auth\AdminForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::post('get/otp', 'Auth\AdminForgotPasswordController@GenerateOTP')->name('password.generate.otp');
    Route::get('password/reset', 'Auth\AdminForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset/pin', 'Auth\AdminResetPasswordController@reset_pin')->name('password.reset.pin');
    Route::post('password/reset', 'Auth\AdminResetPasswordController@reset')->name('password.reset');
    Route::get('password/reset/{token}', 'Auth\AdminResetPasswordController@showResetForm')->name('password.reset');

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::prefix('territory')->name('territory.')->group(function () {
            Route::get('', 'Admins\SalesIncentiveController@territoryindex')->name('territoryindex');
            Route::get('list', 'Admins\SalesIncentiveController@territory_list')->name('list');
            Route::post('users', 'Admins\SalesIncentiveController@territory_users')->name('users');
            Route::post('add', 'Admins\SalesIncentiveController@territory_add')->name('add');
            Route::get('edit/{id}', 'Admins\SalesIncentiveController@territory_edit')->name('edit');
            Route::put('update/{id}', 'Admins\SalesIncentiveController@territory_update')->name('update');
            Route::post('city_admins', 'Admins\SalesIncentiveController@territory_city_admins')->name('city_admins');
            Route::post('status', 'Admins\SalesIncentiveController@territory_enable_disable')->name('status');
        });
        Route::prefix('designation')->name('designation.')->group(function () {
            Route::get('', 'Admins\SalesIncentiveController@designationindex')->name('designationindex');
            Route::get('list', 'Admins\SalesIncentiveController@designation_list')->name('list');
            Route::post('add', 'Admins\SalesIncentiveController@designation_add')->name('add');
            Route::get('edit/{id}', 'Admins\SalesIncentiveController@designation_edit')->name('edit');
            Route::put('update/{id}', 'Admins\SalesIncentiveController@designation_update')->name('update');
            Route::post('status', 'Admins\SalesIncentiveController@designation_enable_disable')->name('status');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {

        Route::prefix('show_vendor')->name('show_vendor.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@show_vendors')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@store_vendors')->name('store');
            // Route::post('udpate', 'Admins\GlobalSettingsController@delivery_revert_access_update')->name('update');
        });

        Route::prefix('delivery_revert_access')->name('delivery_revert_access.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@delivery_revert_access_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@delivery_revert_access_store')->name('store');
            // Route::post('udpate', 'Admins\GlobalSettingsController@delivery_revert_access_update')->name('update');
        });

        Route::prefix('pickup')->name('pickup.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_index')->name('index');
            Route::post('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::put('weight/add', 'Admins\GlobalSettingsController@add_pickup_weight')->name('weight.add');
            Route::get('pickup_settings', 'Admins\GlobalSettingsController@pickup_cut_off_settings_index')->name('pickup_settings');
            Route::post('pickup_settings_store', 'Admins\GlobalSettingsController@pickup_cut_off_settings_store')->name('pickup_settings_store');
            Route::get('weight_bypass', 'Admins\GlobalSettingsController@weight_bypass')->name('weight_bypass');
            Route::post('shipper_store_weight_bypass', 'Admins\GlobalSettingsController@shipper_store_weight_bypass')->name('shipper_store_weight_bypass');
        });

        Route::prefix('shippers')->name('shippers.')->group(function () {

            Route::prefix('base_rate_revisions')->name('base_rate_revisions.')->group(function () {
                Route::get('', 'Admins\Settings\Shippers\BaseRateRivisionController@index')->name('index');
                Route::get('list', 'Admins\Settings\Shippers\BaseRateRivisionController@base_rate_revisions_list')->name('list');
                Route::post('store', 'Admins\Settings\Shippers\BaseRateRivisionController@add_bulk_shipper_rate_adjustment_store')->name('bulk_store');
                Route::get('{base_rate_revision_id}/approval1_update/{action}','Admins\Settings\Shippers\BaseRateRivisionController@approval1_update')->name('approval1_update');
                Route::get('{base_rate_revision_id}/approval2_update/{action}','Admins\Settings\Shippers\BaseRateRivisionController@approval2_update')->name('approval2_update');
                Route::get('shippers_with_rates/{base_rate_revision_id}', 'Admins\Settings\Shippers\BaseRateRivisionController@shippersWithRates')->name('shippers_with_rates');
            });

            Route::prefix('status_webhook')->name('status_webhook.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@status_webhook_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@status_webhook_list')->name('list');
                Route::get('{id}/edit', 'Admins\GlobalSettingsController@status_webhook_edit')->name('edit');
                Route::put('update', 'Admins\GlobalSettingsController@status_webhook_update')->name('update');
            });

            // Route::prefix('mobile_check')->name('mobile_check.')->group(function() {
            //     Route::get('', 'Admins\Settings\GeneralSettingController@mobile_check_index')->name('index');
            //     Route::post('store', 'Admins\Settings\GeneralSettingController@mobile_check_store')->name('store');
            // });

            Route::prefix('bypass_weight')->name('bypass_weight.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@bypass_weight_index')->name('index');
                Route::post('update', 'Admins\GlobalSettingsController@bypass_weight_update')->name('update');

            });

            
            Route::prefix('lead_progress')->name('lead_progress.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@lead_progress_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@lead_progress_list')->name('list');
                Route::post('update', 'Admins\GlobalSettingsController@lead_progress_update')->name('update');
            });
        });

        Route::prefix('fleet')->name('fleet.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@fleet_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@fleet_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@fleet_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@fleet_enable_disable')->name('enable_disable');
            Route::get('unique', 'Admins\GlobalSettingsController@fleet_unique')->name('unique');
            Route::get('{id}/edit/form', 'Admins\GlobalSettingsController@fleet_edit')->name('edit');
            Route::put('{id}/update', 'Admins\GlobalSettingsController@fleet_update')->name('update');
            Route::post('store/driver', 'Admins\GlobalSettingsController@fleet_store_driver')->name('store.driver');
            Route::post('store/vendor', 'Admins\GlobalSettingsController@fleet_store_vendor')->name('store.vendor');
            Route::get('unique/cnic', 'Admins\GlobalSettingsController@fleet_cnic_unique')->name('unique.cnic');
        });

        Route::prefix('route_management')->name('route_management.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@route_management_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@route_management_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@route_management_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@route_management_enable_disable')->name('enable_disable');
            Route::get('unique', 'Admins\GlobalSettingsController@route_management_unique')->name('unique');
            Route::get('{id}/edit/form', 'Admins\GlobalSettingsController@route_management_edit')->name('edit');
            Route::put('{id}/update', 'Admins\GlobalSettingsController@route_management_update')->name('update');
        });

        Route::prefix('shipment_cancellation_cut_off_days')->name('shipment_cancellation_cut_off_days.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipment_cancellation_cut_off_days_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shipment_cancellation_cut_off_days_store')->name('store');
        });
        Route::prefix('auto_account_disabled_days')->name('auto_account_disabled_days.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_account_disabled_days_index')->name('auto_index');
            Route::post('', 'Admins\GlobalSettingsController@auto_account_disabled_days_store')->name('auto_store');
        });

        Route::prefix('daily_pickup_sales_cron')->name('daily_pickup_sales_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@daily_pickup_sales_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@daily_pickup_sales_cron_store')->name('store');
        });

        Route::prefix('non_service_area')->name('non_service_area.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@non_service_area_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@non_service_area_store')->name('store');
        });

        Route::prefix('rv_disable_shippers')->name('rv_disable_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rv_disable_shippers_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rv_disable_shippers_store')->name('store');
        });            

        Route::prefix('ticker')->name('ticker.')->group(function () {
            Route::get('', 'Admins\Settings\GeneralSettingController@ticker_index')->name('index');
            Route::post('', 'Admins\Settings\GeneralSettingController@ticker_store')->name('store');
        });

        Route::prefix('geo_codes')->name('geo_codes.')->group(function () {
            Route::get('', 'GeoCodesController@index')->name('index');
            Route::get('list', 'GeoCodesController@list')->name('list');
            Route::post('get_shipment_lat_long', 'GeoCodesController@get_shipment_lat_long')->name('get_shipment_lat_long');
            Route::get('view_tpl_map', 'GeoCodesController@view_tpl_map')->name('view_tpl_map');

        });

        // //test
        // Route::prefix('shipper')->name('shipper.')->group(function () {
        //     Route::prefix('cap')->name('cap.')->group(function () {
        //         Route::get('', 'Admins\Settings\GeneralSettingController@shipper_cap_index')->name('index');
        //         Route::post('', 'Admins\Settings\GeneralSettingController@tshipper_cap_store')->name('store');
        //     });
        // });

        Route::prefix('rider_ticker')->name('rider_ticker.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_ticker_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_ticker_store')->name('store');
            Route::post('admin_store', 'Admins\GlobalSettingsController@admin_ticker_store')->name('admin_store');
            Route::post('retail_store', 'Admins\GlobalSettingsController@retail_ticker_store')->name('retail_store');
        });

        Route::prefix('walk_in')->name('walk_in.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@walk_in_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@walk_in_store')->name('store');
        });
        Route::prefix('international_walk_in')->name('international_walk_in.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_walk_in_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@international_walk_in_store')->name('store');
        });

        Route::prefix('auto_invoice_generation_and_due_date')->name('auto_invoice_generation_and_due_date.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_invoice_generation_and_due_date_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@auto_invoice_generation_and_due_date_store')->name('store');
        });
        Route::prefix('petty_cash')->name('petty_cash.')->group(function () {
            Route::prefix('heads')->name('heads.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@petty_cash_heads_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@petty_cash_heads_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@petty_cash_heads_add')->name('add');
                Route::post('edit', 'Admins\GlobalSettingsController@petty_cash_heads_edit')->name('edit');
                Route::post('active', 'Admins\GlobalSettingsController@petty_cash_heads_active')->name('active');
                Route::post('inactive', 'Admins\GlobalSettingsController@petty_cash_heads_inactive')->name('inactive');
            });
            Route::prefix('titles')->name('titles.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@petty_cash_titles_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@petty_cash_titles_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@petty_cash_titles_add')->name('add');
                Route::post('info', 'Admins\GlobalSettingsController@petty_cash_titles_info')->name('info');
                Route::post('edit', 'Admins\GlobalSettingsController@petty_cash_titles_edit')->name('edit');
                Route::post('active', 'Admins\GlobalSettingsController@petty_cash_titles_active')->name('active');
                Route::post('inactive', 'Admins\GlobalSettingsController@petty_cash_titles_inactive')->name('inactive');
            });
            Route::prefix('hub-assigning')->name('consignee.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@petty_cash_consignee_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@petty_cash_consignee_list')->name('list');
                Route::Post('', 'Admins\GlobalSettingsController@petty_cash_consignee_store')->name('store');
                Route::Post('edit', 'Admins\GlobalSettingsController@petty_cash_consignee_edit')->name('edit');
                Route::prefix('city')->name('city.')->group(function () {
                    Route::get('{id}', 'Admins\GlobalSettingsController@petty_cash_consignee_city_index')->name('index');
                    Route::Post('{id}/check', 'Admins\GlobalSettingsController@petty_cash_consignee_city_check')->name('check');
                    Route::Post('{id}', 'Admins\GlobalSettingsController@petty_cash_consignee_city_update')->name('update');
                });
            });
        });

        Route::prefix('debriefing_report_cut_off_time')->name('debriefing_report_cut_off_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_report_cut_off_time_store')->name('store');
        });

        Route::prefix('debriefing_break_time')->name('debriefing_break_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_break_time_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_break_time_setting_store')->name('store');
        });

        Route::prefix('fuel_factor')->name('fuel_factor.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@fuel_factor_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@fuel_factor_store')->name('store');
        });

        Route::prefix('return_note_restriction_bypass')->name('return_note_restriction_bypass.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_note_restriction_bypass_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_note_restriction_bypass_store')->name('store');
        });

        Route::prefix('cod_cap_zones')->name('cod_cap_zones.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@cod_cap_zones_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@cod_cap_zones_update')->name('update');
        });

        Route::prefix('product_tax')->name('product_tax.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@product_tax_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@product_tax_update')->name('update');
        });
        Route::prefix('product_tax_logs')->name('product_tax_logs.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@product_tax_logs_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@product_tax_logs')->name('list');
        });
        Route::prefix('ibft_charges')->name('ibft_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@ibft_charges_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@ibft_charges_store')->name('store');
        });

        Route::prefix('weight_factor')->name('weight_factor.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@weight_factor_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@weight_factor_update')->name('update');
        });

        Route::prefix('delivery_call_verification_ratio')->name('delivery_call_verification_ratio.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@delivery_call_verification_ratio_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@delivery_call_verification_ratio_update')->name('update');
        });

        Route::prefix('stock_movement')->name('stock_movement.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@stock_movement_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@stock_movement_update')->name('update');
        });

        Route::prefix('crm_cut_off_time_and_holidays')->name('crm_cut_off_time_and_holidays.')->group(function () {
            Route::get('', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_index')->name('index');
            Route::post('update', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_update')->name('update');
            Route::post('list', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_list')->name('list');
            Route::post('add', 'Admins\AdminCrmSettingsController@crm_cut_off_time_and_holidays_add')->name('add');
        });


        Route::prefix('csat_cases_setting')->name('csat_cases_setting.')->group(function () {
            Route::get('', 'Admins\AdminCrmSettingsController@csat_cases_setting_index')->name('index');
            Route::get('formula', 'Admins\AdminCrmSettingsController@csat_score_formula_index')->name('formula.index');
            Route::post('formula', 'Admins\AdminCrmSettingsController@csat_score_formula_store')->name('formula.store');

            Route::post('/submit', 'Admins\AdminCrmSettingsController@csat_cases_setting_store')->name('store');

        });

        Route::prefix('return_confirmation_pending_shipment_selection_time')->name('return_confirmation_pending_shipment_selection_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_confirmation_pending_shipment_selection_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_confirmation_pending_shipment_selection_time_store')->name('store');
        });

        Route::prefix('consolidation')->name('consolidation.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@consolidation_max_shipments_index')->name('max.index');
            Route::post('update', 'Admins\GlobalSettingsController@consolidation_max_shipments_update')->name('max.update');
        });

        Route::prefix('crm_case_nature_types')->name('crm_case_nature_types.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_case_nature_types_index')->name('index');
            Route::post('list', 'Admins\GlobalSettingsController@crm_case_nature_types_list')->name('list');
            Route::post('status', 'Admins\GlobalSettingsController@crm_case_nature_types_status')->name('status');
            Route::post('store', 'Admins\GlobalSettingsController@crm_case_nature_types_store')->name('store');

            Route::get('add', 'Admins\GlobalSettingsController@crm_case_nature_types_add_form')->name('add');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@crm_case_nature_types_edit_form')->name('edit');
            Route::get('edit/ajax/{id}', 'Admins\GlobalSettingsController@crm_case_nature_types_edit_ajax_list')->name('edit_ajax');

            Route::post('update', 'Admins\GlobalSettingsController@crm_case_nature_types_update')->name('update');
        });

        Route::prefix('return_delivered_to_shipper_email_cut_off_time')->name('return_delivered_to_shipper_email_cut_off_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_delivered_to_shipper_email_cut_off_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_delivered_to_shipper_email_cut_off_time_store')->name('store');
        });

        Route::prefix('crm_reopen')->name('crm_reopen.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_reopen_count_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@crm_reopen_count_submit')->name('update');
        });

        Route::prefix('multiple_sale_tagging')->name('multiple_sale_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@multiple_sale_tagging_index')->name('index');
            Route::post('list', 'Admins\GlobalSettingsController@multiple_sale_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@multiple_sale_tagging_submit')->name('submit');
            Route::post('assign_admin/view', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_view')->name('assign_admin.view');
            Route::post('assign_admin/submit', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_submit')->name('assign_admin.submit');
            Route::post('assign_admin/view_assigned', 'Admins\GlobalSettingsController@multiple_sale_tagging_assign_view_assigned')->name('assign_admin.view_assigned');
        });

        Route::prefix('foc_account')->name('foc_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@foc_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@foc_account_store')->name('store');
        });

        Route::prefix('rv_shipper_priority')->name('rv_shipper_priority.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rv_shipper_priority_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@rv_shipper_priority_store')->name('store');
            });

        Route::prefix('mms_report_setting')->name('mms_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@mms_report_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@mms_report_store')->name('store');
        });

        Route::prefix('invoice_against_return_delivered_shipper')->name('invoice_against_return_delivered_shipper.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@invoice_against_return_delivered_shipper_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@invoice_against_return_delivered_shipper_store')->name('store');
        });

        Route::prefix('ccd_booking')->name('ccd_booking.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@ccd_booking_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@ccd_booking_store')->name('store');
        });

        Route::prefix('project_arrival_shippers')->name('project_arrival_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@project_arrival_shippers_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@project_arrival_shippers_store')->name('store');
        });

        Route::prefix('nsa_account')->name('nsa_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@nsa_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@nsa_account_store')->name('store');
        });

        Route::prefix('carrefour_account')->name('carrefour_account.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@carrefour_account_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@carrefour_account_store')->name('store');
        });

        Route::prefix('restrict_cities_intercept')->name('restrict_cities_intercept.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@restrict_cities_intercept_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@restrict_cities_intercept_store')->name('store');
        });

        Route::prefix('minimum_chargeable_weight')->name('minimum_chargeable_weight.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@minimum_chargeable_weight_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@minimum_chargeable_weight_update')->name('update');
        });

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::prefix('incentive')->name('incentive.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_incentive')->name('index');
                Route::post('add', 'Admins\GlobalSettingsController@sales_incentive_add')->name('add');
            });
            Route::prefix('targets')->name('targets.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_person_targets')->name('index');
                Route::post('', 'Admins\GlobalSettingsController@sales_person_targets_submit')->name('update');
                Route::get('list', 'Admins\GlobalSettingsController@sales_person_targets_list')->name('list');
                Route::post('delete_sale_person_targets', 'Admins\GlobalSettingsController@delete_sale_person_targets')->name('delete');
            });
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_person_targets_history')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@sales_person_targets_history_list')->name('list');
            });

            Route::prefix('key_accounts')->name('key_accounts.')->group(function () {
                Route::get('', 'Admins\AdminSalesController@key_accounts_dashboard_index')->name('dashboard');
                Route::post('details', 'Admins\AdminSalesController@key_accounts_dashboard_details')->name('dashboard.details');
            });

            Route::prefix('user_restriction')->name('user_restriction.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@sales_user_restriction_index')->name('index');
                Route::post('store', 'Admins\GlobalSettingsController@sales_user_restriction_store')->name('store');
            });


            Route::prefix('projection')->name('projection.')->group(function () {
                Route::prefix('percentage')->name('percentage.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_percentage_index')->name('index');
                    Route::post('', 'Admins\GlobalSettingsController@projection_percentage_update')->name('store');
                });
                Route::prefix('reasons')->name('reasons.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_reason_index')->name('index');
                    Route::get('list', 'Admins\GlobalSettingsController@projection_reason_list')->name('list');
                    Route::post('', 'Admins\GlobalSettingsController@projection_reason_update')->name('store');
                });

                Route::prefix('shipments')->name('shipments.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@projection_shipments_index')->name('index');
                    Route::post('', 'Admins\GlobalSettingsController@projection_shipments_update')->name('store');
                    Route::get('list', 'Admins\GlobalSettingsController@projection_shipments_list')->name('list');
                });
            });
        });

        Route::prefix('aging_report')->name('aging_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@completed_aging_report_settings_index')->name('index');
            Route::post('aging_settings_store', 'Admins\GlobalSettingsController@completed_aging_report_settings_store')->name('aging_settings_store');
        });

        Route::prefix('zero_charges')->name('zero_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@zero_charges_report_settings_index')->name('index');
            Route::post('zero_charges_store', 'Admins\GlobalSettingsController@zero_charges_report_settings_store')->name('zero_charges_store');
        });

        Route::prefix('overnight_overland_cargo_report')->name('overnight_overland_cargo_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_list')->name('list');
            Route::post('update', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_rad_tat_submit')->name('update');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_edit_index')->name('edit');
            Route::post('edit/update', 'Admins\GlobalSettingsController@overnight_overland_cargo_report_origin_submit')->name('edit.update');
        });

        Route::prefix('delay_in_delivery_massage')->name('delay_in_delivery_massage.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@delay_in_delivery_massage')->name('index');
            Route::post('submit', 'Admins\GlobalSettingsController@delay_in_delivery_massage_store')->name('store');
        });

        Route::prefix('crm_comment')->name('crm_comment.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_crm_comment_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@auto_crm_comment_store')->name('store');
        });

        Route::prefix('default_agent')->name('default_agent.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_default_agent_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@crm_default_agent_store')->name('store');
        });

        Route::prefix('auto_assigning')->name('auto_assigning.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_auto_assigning_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@crm_auto_assigning_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@crm_auto_assigning_submit')->name('submit');
            Route::get('/edit/{id}', 'Admins\GlobalSettingsController@crm_auto_assigning_edit')->name('edit');
            Route::post('data', 'Admins\GlobalSettingsController@crm_auto_assigning_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@crm_auto_assigning_update')->name('update');
            Route::post('delete', 'Admins\GlobalSettingsController@crm_auto_assigning_delete')->name('delete');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@crm_auto_assigning_enable_disable')->name('enable_disable');
            Route::post('get_hub', 'Admins\GlobalSettingsController@get_hub')->name('get_hub');
            Route::post('case_nature_type', 'Admins\GlobalSettingsController@case_nature_type')->name('case_nature_type');
            Route::get('add', 'Admins\GlobalSettingsController@add_auto_assign_agent')->name('add');
            Route::get('global_status', 'Admins\GlobalSettingsController@global_status')->name('global_status');
            Route::post('get_sub_segments', 'Admins\GlobalSettingsController@get_sub_segments')->name('get_sub_segments');
            Route::post('get_origin_areas','Admins\GlobalSettingsController@get_origin_areas')->name('get_origin_areas');
            Route::post('get_origin_hub','Admins\GlobalSettingsController@get_origin_hub')->name('get_origin_hub');
            Route::get('get_shipper_key/{agent_id}', 'Admins\GlobalSettingsController@get_shipper_key')->name('get_shipper_key');
            Route::get('get_shipper_non_key', 'Admins\GlobalSettingsController@get_shipper_non_key')->name('get_shipper_non_key');

        });

        Route::prefix('auto_tagging')->name('auto_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@crm_auto_tagging_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@crm_auto_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@crm_auto_tagging_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@crm_auto_tagging_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@crm_auto_tagging_update')->name('update');
            Route::post('delete', 'Admins\GlobalSettingsController@crm_auto_tagging_delete')->name('delete');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@crm_auto_tagging_enable_disable')->name('enable_disable');
            Route::post('hub_areas', 'Admins\GlobalSettingsController@hub_areas')->name('hub_areas');
            Route::post('case_nature_types', 'Admins\GlobalSettingsController@case_nature_types')->name('case_nature_types');

            Route::get('crm_auto_tagging_get_dept_wise_agents', 'Admins\GlobalSettingsController@crm_auto_tagging_get_dept_wise_agents')->name('crm_auto_tagging_get_dept_wise_agents');

        });

        Route::prefix('blacklist')->name('blacklist.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@blacklist_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@blacklist_list')->name('list');
            Route::get('add', 'Admins\GlobalSettingsController@blacklist_add')->name('add');
            Route::post('add', 'Admins\GlobalSettingsController@blacklist_add_store')->name('add');
            Route::post('unique', 'Admins\GlobalSettingsController@blacklist_unique_criteria')->name('unique');
            Route::post('status', 'Admins\GlobalSettingsController@blacklist_status')->name('status');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@blacklist_edit')->name('edit');
            Route::post('edit/{id}', 'Admins\GlobalSettingsController@blacklist_edit_submit')->name('edit');
            Route::prefix('search')->name('search.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@blacklist_search_index')->name('index');
                Route::post('consignee', 'Admins\GlobalSettingsController@blacklist_search_consignee')->name('consignee');
                Route::post('update', 'Admins\GlobalSettingsController@blacklist_search_update')->name('update');
            });
        });

        //commission routes
        Route::prefix('commission')->name('commission.')->group(function () {
            Route::get('', 'Admins\AdminCommissionController@index')->name('index');
            Route::get('list', 'Admins\AdminCommissionController@tier_list')->name('list');
            Route::post('add', 'Admins\AdminCommissionController@add_sales_tier')->name('add');
            Route::post('status', 'Admins\AdminCommissionController@commission_status')->name('status');
            Route::post('details', 'Admins\AdminCommissionController@editSalesTierView')->name('details');
            Route::post('edit', 'Admins\AdminCommissionController@editSalesTier')->name('edit');
            Route::get('set_commission/{id?}', 'Admins\AdminCommissionController@setCommission')->name('set_commission');
            Route::post('set_commission', 'Admins\AdminCommissionController@set_commission_submit')->name('set_commission.submit');
            Route::get('approve_commission/{id?}', 'Admins\AdminCommissionController@approveCommission')->name('approve_commission');
            Route::post('approve_commission', 'Admins\AdminCommissionController@approve_commission_submit')->name('approve_commission.submit');
            Route::prefix('percentage')->name('percentage.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@commission_percentage_index')->name('index');
                Route::post('', 'Admins\GlobalSettingsController@commission_percentage_update')->name('store');
            });
        });

        Route::prefix('return')->name('return.')->group(function () {
            Route::prefix('reason')->name('reason.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@return_reason_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@return_reason_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@return_reason_add')->name('add');
                Route::post('get', 'Admins\GlobalSettingsController@return_reason_get')->name('get');
                Route::post('edit', 'Admins\GlobalSettingsController@return_reason_edit')->name('edit');
            });
        });

        Route::prefix('escalation')->name('escalation.')->group(function () {
            Route::prefix('launched')->name('launched.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_launched_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_launched_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_launched_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_launched_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_launched_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_launched_edit_store')->name('edit.store');
            });
            Route::prefix('in_process')->name('in_process.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_in_process_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_in_process_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_in_process_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_in_process_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_in_process_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_in_process_edit_store')->name('edit.store');
            });
            Route::prefix('tagging')->name('tagging.')->group(function () {
                Route::get('', 'Admins\AdminCrmSettingsController@escalation_tagging_index')->name('index');
                Route::get('/list', 'Admins\AdminCrmSettingsController@escalation_tagging_list')->name('list');
                Route::get('/add', 'Admins\AdminCrmSettingsController@escalation_tagging_add_index')->name('add.index');
                Route::post('/add/store', 'Admins\AdminCrmSettingsController@escalation_tagging_add_store')->name('add.store');
                Route::get('/edit/{id}', 'Admins\AdminCrmSettingsController@escalation_tagging_edit_index')->name('edit.index');
                Route::post('/edit/store', 'Admins\AdminCrmSettingsController@escalation_tagging_edit_store')->name('edit.store');
                Route::post('/status', 'Admins\AdminCrmSettingsController@escalation_tagging_status_update')->name('status');
                Route::post('/view_hubs', 'Admins\AdminCrmSettingsController@escalation_tagging_view_hubs')->name('view_hubs');
                Route::post('/view_statuses', 'Admins\AdminCrmSettingsController@escalation_tagging_view_statuses')->name('view_statuses');
                Route::post('/view_levels', 'Admins\AdminCrmSettingsController@escalation_tagging_view_levels')->name('view_levels');
            });
            Route::post('/status', 'Admins\AdminCrmSettingsController@escalation_status_update')->name('status');
            Route::post('/view_statuses', 'Admins\AdminCrmSettingsController@escalation_view_statuses')->name('view_statuses');
            Route::get('/levels', 'Admins\AdminCrmSettingsController@escalation_level_index')->name('levels.index');
            Route::post('/levels/store', 'Admins\AdminCrmSettingsController@escalation_level_store')->name('levels.store');
        });

        Route::prefix('holidays')->name('holidays.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@holidays_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@holidays_update')->name('update');
            Route::post('list', 'Admins\GlobalSettingsController@holidays_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@holidays_add')->name('add');
        });

        Route::prefix('not_attempted_cron')->name('not_attempted_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@not_attempted_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@not_attempted_cron_store')->name('store');
        });

        Route::prefix('station_recovery_cron')->name('station_recovery_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@station_recovery_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@station_recovery_cron_store')->name('store');
        });

        Route::prefix('over_payment_limit')->name('over_payment_limit.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@over_payment_limit_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@over_payment_limit_store')->name('store');
        });

        Route::prefix('short_received_hub_wise_cron')->name('short_received_hub_wise_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@short_received_hub_wise_cron_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@short_received_hub_wise_cron_store')->name('store');
        });

        Route::prefix('restrict_parcels_attempt')->name('restrict_parcels_attempt.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@restrict_parcels_attempt_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@restrict_parcels_attempt_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@restrict_parcels_attempt_add')->name('add');
            Route::post('edit', 'Admins\GlobalSettingsController@restrict_parcels_attempt_edit')->name('edit');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@restrict_parcels_attempt_enable_disable')->name('enable_disable');
        });

        Route::prefix('runner')->name('runner.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@runner_report_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@runner_report_list')->name('list');
            Route::get('unique', 'Admins\GlobalSettingsController@runner_report_unique')->name('unique');
            Route::post('add', 'Admins\GlobalSettingsController@runner_report_add')->name('add');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@runner_report_enable_disable')->name('enable_disable');
        });

        Route::prefix('sms_shipper_wise')->name('sms_shipper_wise.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@arrived_at_origin_sms_for_shipper_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@arrived_at_origin_sms_for_shipper_update')->name('update');
        });

        Route::prefix('pickup_address_wise_payment_accounts')->name('pickup_address_wise_payment_accounts.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@pickup_address_wise_payment_accounts_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@pickup_address_wise_payment_accounts_submit')->name('store');
        });

        Route::prefix('onelink_payment_charges')->name('onelink_payment_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@onelink_payment_charges_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@onelink_payment_charges_submit')->name('store');
            // Route::post('store', 'Admins\GlobalSettingsController@pickup_address_wise_payment_accounts_submit')->name('store');
        });

        Route::prefix('fintech_company_charges')->name('fintech_company_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@setup_fintech_charges_index')->name('index');
            Route::get('add', 'Admins\GlobalSettingsController@setup_fintech_charges_show')->name('form');
            Route::get('list', 'Admins\GlobalSettingsController@setup_fintech_charges_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@setup_fintech_charges_save')->name('store');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@setup_fintech_charges_edit')->name('edit');
            Route::post('edit_save', 'Admins\GlobalSettingsController@setup_fintech_charges_edit_save')->name('edit_save');
            Route::get('edit_status', 'Admins\GlobalSettingsController@change_company_status')->name('status');
        });

        Route::prefix('standard_fintech_charges')->name('standard_fintech_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@standard_fintech_charges_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@standard_fintech_charges_store')->name('store');
        });

        Route::prefix('month_closing')->name('month_closing.')->group(function () {
            Route::prefix('types')->name('types.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@month_closing_type_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@month_closing_type_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@month_closing_type_add')->name('add');
                Route::post('edit', 'Admins\GlobalSettingsController@month_closing_type_edit')->name('edit');
            });
            Route::prefix('status')->name('status.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@month_closing_status_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@month_closing_status_list')->name('list');
                Route::post('add', 'Admins\GlobalSettingsController@month_closing_status_add')->name('add');
                Route::post('edit', 'Admins\GlobalSettingsController@month_closing_status_edit')->name('edit');
            });
        });

        Route::prefix('international_rates')->name('international_rates.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_rates_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@international_rates_update')->name('update');

            Route::prefix('upload')->name('upload.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@international_rates_upload_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@international_standard_dhl_rates_list')->name('list');
                Route::post('excel', 'Admins\GlobalSettingsController@international_rates_upload_excel')->name('excel');
            });
        });

        Route::prefix('hr')->name('hr.')->group(function () {
            Route::prefix('rider_incentive')->name('rider_incentive.')->group(function () {
                Route::get('', 'Admins\GlobalSettingsController@rider_incentive_index')->name('index');
                Route::get('list', 'Admins\GlobalSettingsController@rider_incentive_list')->name('list');
                Route::post('store', 'Admins\GlobalSettingsController@rider_incentive_store')->name('store');
                Route::get('details', 'Admins\GlobalSettingsController@rider_incentive_details')->name('details');
                Route::post('update', 'Admins\GlobalSettingsController@rider_incentive_update')->name('update');

                Route::prefix('cron')->name('cron.')->group(function () {
                    Route::get('', 'Admins\GlobalSettingsController@rider_incentive_cron_index')->name('index');
                    Route::post('store', 'Admins\GlobalSettingsController@rider_incentive_cron_store')->name('store');
                });
            });
        });

        Route::prefix('return_confirmation_pending_tat_setting')->name('rcp_tat.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rcp_tat_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@rcp_tat_list')->name('list');
            Route::put('update', 'Admins\GlobalSettingsController@rcp_tat_update')->name('update');
        });

        Route::prefix('return_confirmation_pending_sms_setting')->name('rcp_sms.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rcp_sms_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@rcp_sms_update')->name('update');
        });

        Route::prefix('debriefing_time_setting')->name('debriefing_time_setting.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_time_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_time_setting_update')->name('update');
        });

        Route::prefix('debriefing_role_setting')->name('debriefing_role_setting.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@debriefing_role_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@debriefing_role_setting_update')->name('update');

        });

        Route::prefix('sms_notifications_limit')->name('sms_notifications_limit.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@sms_notifications_limit_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@sms_notifications_limit_update')->name('update');
        });
        
        Route::prefix('disable_email_on_arrival')->name('disable_email_on_arrival.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@disable_email_on_arrival_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@disable_email_on_arrival_update')->name('update');
        });

        Route::prefix('omni')->name('omni.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@omni_user_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@omni_user_setting_update')->name('update');
        });

        Route::prefix('shipment_status_eta')->name('shipment_status_eta.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipment_status_eta_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@shipment_status_eta_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@shipment_status_eta_store')->name('store');
            Route::post('edit', 'Admins\GlobalSettingsController@shipment_status_eta_edit')->name('edit');
        });

        Route::prefix('rider_shipment_attempt')->name('rider_shipment_attempt.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_shipment_attempt_settings_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_shipment_attempt_settings_store')->name('store');
        });

        Route::prefix('rider_deactivation_cron')->name('rider_deactivation_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_deactivation_cron_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@rider_deactivation_cron_store')->name('store');
        });

        Route::prefix('bolt_update_version')->name('bolt_update_version.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@bolt_update_version_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@bolt_update_version_store')->name('store');
        });

        Route::prefix('last_mile_cron')->name('last_mile_cron.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@last_mile_cron_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@last_mile_cron_store')->name('store');
        });

        Route::prefix('dhl_sync_time')->name('dhl_sync_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@dhl_sync_time_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@dhl_sync_time_store')->name('store');
        });

        Route::prefix('international_automation_user')->name('international_automation_user.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@international_automation_user_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@international_automation_user_store')->name('store');
        });

        Route::prefix('reattempt_percentage')->name('reattempt_percentage.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@reattempt_percentage_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@reattempt_percentage_store')->name('store');
        });

        Route::prefix('lead_tagging')->name('lead_tagging.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_tagging_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_tagging_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lead_tagging_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@lead_tagging_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_tagging_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_tagging_enable_disable')->name('enable_disable');
            Route::post('services', 'Admins\GlobalSettingsController@lead_tagging_services')->name('services');
        });

        Route::prefix('lead_zones')->name('lead_zones.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_zones_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_zones_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lead_zones_submit')->name('submit');
            Route::post('data', 'Admins\GlobalSettingsController@lead_zones_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_zones_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_zones_enable_disable')->name('enable_disable');
        });

        Route::prefix('lead_notification')->name('lead_notification.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lead_notification_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lead_notification_list')->name('list');
            Route::post('data', 'Admins\GlobalSettingsController@lead_notification_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@lead_notification_update')->name('update');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@lead_notification_enable_disable')->name('enable_disable');
            Route::post('delete_image', 'Admins\GlobalSettingsController@lead_notification_delete_image')->name('delete_image');
        });

        Route::prefix('shippers_origin_change')->name('shippers_origin_change.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipper_origin_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shipper_origin_store')->name('store');
        });

        Route::prefix('shippers_return_address')->name('shippers_return_address.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shippers_return_address_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@shippers_return_address_store')->name('store');
        });

        Route::prefix('consignee_sms_expire')->name('consignee_sms_expire.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@consignee_sms_expire_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@consignee_sms_expire_store')->name('store');
        });

        Route::prefix('return_reason_mandatory')->name('return_reason_mandatory.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_reason_mandatory_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@return_reason_mandatory_list')->name('list');
            Route::post('store', 'Admins\GlobalSettingsController@return_reason_mandatory_store')->name('store');
        });

        Route::prefix('return_shipments_address')->name('return_shipments_address.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@return_shipments_address_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@return_shipments_address_store')->name('store');
        });

        Route::prefix('auto_tag_territories')->name('auto_tag_territories.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_tag_territories_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@auto_tag_territories_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@auto_tag_territories_store')->name('submit');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@auto_tag_territories_enable_disable')->name('enable_disable');
            Route::post('data', 'Admins\GlobalSettingsController@auto_tag_territories_data')->name('data');
            Route::post('update', 'Admins\GlobalSettingsController@auto_tag_territories_update')->name('update');
        });

        Route::prefix('cn_print_right')->name('cn_print_right.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@cn_print_right')->name('cn_print_right');
            Route::post('store', 'Admins\GlobalSettingsController@cn_print_right_store')->name('store');
        });

        Route::prefix('referral')->name('referral.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@referral')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@referral_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@referral_store')->name('submit');
            Route::get('name', 'Admins\GlobalSettingsController@referral_name')->name('name');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@referral_enable_disable')->name('enable_disable');
        });

        Route::prefix('lost_shipment_shippers')->name('lost_shipment_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lost_shipment_shippers_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lost_shipment_shippers_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lost_shipment_shippers_add')->name('add');
            Route::post('delete', 'Admins\GlobalSettingsController@lost_shipment_shippers_delete')->name('delete');
        });

        Route::prefix('lost_shipment_admins')->name('lost_shipment_admins.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@lost_shipment_admins_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@lost_shipment_admins_list')->name('list');
            Route::post('submit', 'Admins\GlobalSettingsController@lost_shipment_admins_add')->name('add');
            Route::post('delete', 'Admins\GlobalSettingsController@lost_shipment_admins_delete')->name('delete');
        });

        Route::prefix('undelivered_sms_hub_wise')->name('undelivered_sms_hub_wise.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@undelivered_sms_hub_wise')->name('index');
            Route::post('submit', 'Admins\GlobalSettingsController@undelivered_sms_hub_wise_submit')->name('submit');
        });

        Route::prefix('delivery_area_keyword')->name('delivery_area_keyword.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@delivery_area_keyword')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@delivery_area_keyword_list')->name('list');
            Route::get('add', 'Admins\GlobalSettingsController@delivery_area_keyword_add')->name('add');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@delivery_area_keyword_edit')->name('edit');
            Route::get('view/{id}', 'Admins\GlobalSettingsController@delivery_area_keyword_view')->name('view');
            Route::post('store', 'Admins\GlobalSettingsController@delivery_area_keyword_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@delivery_area_keyword_enable_disable')->name('enable_disable');
            Route::post('update', 'Admins\GlobalSettingsController@delivery_area_keyword_update')->name('update');
            Route::get('get_city_area', 'Admins\GlobalSettingsController@get_city_area')->name('get_city_area');
        });

        Route::prefix('booking_destination_keyword')->name('booking_destination_keyword.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@booking_destination_keyword')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@booking_destination_keyword_list')->name('list');
            Route::get('add', 'Admins\GlobalSettingsController@booking_destination_keyword_add')->name('add');
            Route::get('edit/{id}', 'Admins\GlobalSettingsController@booking_destination_keyword_edit')->name('edit');
            Route::get('view/{id}', 'Admins\GlobalSettingsController@booking_destination_keyword_view')->name('view');
            Route::post('store', 'Admins\GlobalSettingsController@booking_destination_keyword_store')->name('store');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@booking_destination_keyword_enable_disable')->name('enable_disable');
            Route::post('update', 'Admins\GlobalSettingsController@booking_destination_keyword_update')->name('update');
            Route::get('address_verify', 'Admins\GlobalSettingsController@address_verify')->name('address_verify');
        });

        Route::prefix('complain_portal_shippers')->name('complain_portal_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@complain_portal_shippers')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@complain_portal_shippers_update')->name('update');
        });

        Route::prefix('non_cod_otp_shippers')->name('non_cod_otp_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@non_cod_otp_shippers_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@non_cod_otp_shippers_store')->name('store');
        });

        Route::prefix('consignee_refused_otp_bypass')->name('consignee_refused_otp_bypass.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@consignee_refused_otp_bypass_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@consignee_refused_otp_bypass_store')->name('store');
        });

        Route::prefix('auto_delivery_note_verification')->name('auto_delivery_note_verification.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@auto_delivery_note_verification_index')->name('index');
            Route::post('store', 'Admins\GlobalSettingsController@auto_delivery_note_verification_store')->name('store');
        });

        Route::prefix('star_shippers')->name('star_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@star_shippers_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@star_shippers_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@star_shippers_add')->name('add');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@star_shippers_enable_disable')->name('enable_disable');
        });

        Route::prefix('wallet_shippers')->name('wallet_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@wallet_shippers_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@wallet_shippers_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@wallet_shippers_add')->name('add');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@wallet_shippers_enable_disable')->name('enable_disable');
        });
        Route::prefix('alist_shippers')->name('alist_shippers.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@aListShippersIndex')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@aListShipperView')->name('list');
        });

        Route::prefix('airway_bill_address_visibility')->name('airway_bill_address_visibility.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@airway_bill_address_visibility_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@airway_bill_address_visibility_store')->name('store');
        });

        Route::prefix('background_image')->name('background_image.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@background_image_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@background_image_store')->name('store');
        });

        Route::prefix('rider_assigned_hub')->name('rider_assigned_hub.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@rider_assigned_hub_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@rider_assigned_hub_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@rider_assigned_hub_add')->name('add');
            Route::post('edit', 'Admins\GlobalSettingsController@rider_assigned_hub_edit')->name('edit');
            Route::post('submit', 'Admins\GlobalSettingsController@rider_assigned_hub_edit_submit')->name('edit.submit');
            Route::post('enable_disable', 'Admins\GlobalSettingsController@rider_assigned_hub_enable_disable')->name('enable_disable');
            Route::post('hub_count', 'Admins\GlobalSettingsController@rider_assigned_hub_count')->name('hub_count');
        });

        Route::prefix('parcel_value_bypass')->name('parcel_value_bypass.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@parcel_value_bypass_setting_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@parcel_value_bypass_setting_update')->name('update');
        });

        Route::prefix('product_type')->name('product_type.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@product_type_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@product_type_list')->name('list');
            Route::post('add', 'Admins\GlobalSettingsController@product_type_add')->name('add');
            Route::post('edit', 'Admins\GlobalSettingsController@product_type_edit')->name('edit');
            Route::post('delete', 'Admins\GlobalSettingsController@product_type_delete')->name('delete');
        });
        Route::prefix('logistic_report_setting')->name('logistic_report.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@logistic_report_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@logistic_report_store')->name('store');

        });

        Route::prefix('shipper_ibft_charges_settings')->name('shipper_ibft_charges_settings.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipper_ibft_charges_settings_index')->name('index');
            Route::get('list', 'Admins\GlobalSettingsController@shipper_ibft_charges_settings_list')->name('list');
            Route::post('update', 'Admins\GlobalSettingsController@shipper_ibft_charges_settings_update')->name('update');
        });

        Route::prefix('shipper_negative_payable')->name('shipper_negative_payable.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@shipper_negative_payable_index')->name('index');
            Route::post('update', 'Admins\GlobalSettingsController@shipper_negative_payable_update')->name('update');
        });

        Route::prefix('mms_excel_booking_setting')->name('mms_excel_booking_setting.')->group(function () {
            Route::get('', 'Admins\Settings\GeneralSettingController@mms_excel_booking_setting_index')->name('index');
            Route::post('', 'Admins\Settings\GeneralSettingController@mms_excel_booking_setting_store')->name('store');
        });
        Route::prefix('shipper_cap')->name('shipper_cap.')->group(function () {
            Route::get('', 'Admins\Settings\GeneralSettingController@shipper_cap_index')->name('index');
            Route::get('list', 'Admins\Settings\GeneralSettingController@shipper_cap_list')->name('list');
            Route::post('store', 'Admins\Settings\GeneralSettingController@shipper_cap_store')->name('store');
            Route::post('edit', 'Admins\Settings\GeneralSettingController@shipper_cap_edit')->name('edit');
            Route::post('update', 'Admins\Settings\GeneralSettingController@shipper_cap_update')->name('update');

        });


        //Agents List
        Route::prefix('agents_list')->name('agents_list.')->group(function () {
            Route::get('', 'Admins\Settings\AgentSettingsController@agents_list_index')->name('index');
            Route::get('list', 'Admins\Settings\AgentSettingsController@agents_list_list')->name('list');
            Route::post('store', 'Admins\Settings\AgentSettingsController@agent_type_store')->name('store');
            Route::post('data', 'Admins\Settings\AgentSettingsController@agent_data')->name('data');
            Route::post('update', 'Admins\Settings\AgentSettingsController@admin_agent_type_update')->name('update');
            Route::post('bulk-update', 'Admins\Settings\AgentSettingsController@admin_agent_type_update_bulk')->name('update.bulk');

        });

        //Agent Types
        Route::prefix('agent_types')->name('agent_types.')->group(function () {
            Route::get('', 'Admins\Settings\AgentSettingsController@agent_types_index')->name('index');
            Route::get('list', 'Admins\Settings\AgentSettingsController@agent_types_list')->name('list');
            Route::post('store', 'Admins\Settings\AgentSettingsController@agent_type_store')->name('store');
            Route::post('data', 'Admins\Settings\AgentSettingsController@agent_types_data')->name('data');
            Route::post('update', 'Admins\Settings\AgentSettingsController@agent_type_update')->name('update');

        });

        Route::prefix('faf_charges')->name('faf_charges.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@faf_charges_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@faf_charges_store')->name('store');
        });

        // Route::prefix('reports/email_delivery_time')->name('reports.email_delivery_time.')->group(function () {
        //     Route::get('', 'Admins\GlobalSettingsController@email_delivery_time_index')->name('index');
        //     Route::post('', 'Admins\GlobalSettingsController@email_delivery_time_update')->name('update');
        // });
        Route::prefix('email_delivery_time')->name('email_delivery_time.')->group(function () {
            Route::get('', 'Admins\GlobalSettingsController@email_delivery_time_index')->name('index');
            Route::post('', 'Admins\GlobalSettingsController@email_delivery_time_update')->name('update');
        });

    });

    Route::prefix('shipment')->name('shipment.')->group(function () {
        Route::prefix('book')->name('book.')->group(function () {
            Route::get('', 'Admins\AdminWalkInBookShipmentController@index')->name('walk_in');
            //            Route::get('order_id', 'Admins\AdminWalkInBookShipmentController@order_id')->name('order_id');
            Route::post('store', 'Admins\AdminWalkInBookShipmentController@walk_in_store')->name('store');
            Route::post('add_fuel_surcharge_gst_total', 'Admins\AdminWalkInBookShipmentController@add_fuel_surcharge_gst_total')->name('add_fuel_surcharge_gst_total');
            Route::post('print_air_waybill', 'Admins\AdminWalkInBookShipmentController@print_air_waybill')->name('print_air_waybill');
            Route::post('check_standard_weight', 'Admins\AdminWalkInBookShipmentController@check_standard_weight')->name('check_standard_weight');
            Route::post('check_min_charges', 'Admins\AdminWalkInBookShipmentController@check_min_charges')->name('check_min_charges');
            Route::post('check_quantity', 'Admins\AdminWalkInBookShipmentController@check_packing_quantity')->name('check_quantity');
            Route::get('international', 'Admins\AdminWalkInBookShipmentController@international_book_index')->name('international_walk_in');
            Route::post('international_store', 'Admins\AdminWalkInBookShipmentController@international_walk_in_store')->name('international_store');
            Route::post('check_international_min_charges', 'Admins\AdminWalkInBookShipmentController@check_international_min_charges')->name('check_international_min_charges');
            Route::post('check_international_standard_weight', 'Admins\AdminWalkInBookShipmentController@check_international_standard_weight')->name('check_international_standard_weight');

            Route::prefix('ftl')->name('ftl.')->group(function () {
                Route::get('', 'Admins\AdminWalkInBookShipmentController@ftl_book_index')->name('walk_in');
                Route::post('', 'Admins\AdminWalkInBookShipmentController@get_ftl_info')->name('get_ftl_info');
                Route::post('store', 'Admins\AdminWalkInBookShipmentController@ftl_store')->name('store');
                Route::post('print_ftl_air_waybill', 'Admins\AdminWalkInBookShipmentController@print_ftl_air_waybill')->name('print_air_waybill');
            });
        });
        Route::prefix('history')->name('history.')->group(function () {
            Route::get('', 'Admins\AdminWalkInBookShipmentController@history_index')->name('walk_in_history');
            Route::get('list', 'Admins\AdminWalkInBookShipmentController@history_list')->name('walk_in_history_list');
        });
        Route::prefix('consolidation')->name('consolidation.')->group(function () {
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('', 'Admins\AdminConsolidatedController@consolidation_history_index')->name('index');
                Route::get('list', 'Admins\AdminConsolidatedController@consolidation_history_list')->name('list');
            });
        });
        Route::prefix('receiving_sheet')->name('receiving_sheet.')->group(function () {
            Route::get('', 'Admins\AdminReceivingSheetHistoryController@receiving_sheet_index')->name('index');
            Route::get('list', 'Admins\AdminReceivingSheetHistoryController@receiving_sheet_list')->name('list');
            Route::post('print', 'Admins\AdminReceivingSheetHistoryController@print')->name('print');
        });
        Route::prefix('poc_kam_tagged_accounts')->name('poc_kam_tagged_accounts.')->group(function () {
            Route::get('', 'Admins\AdminTaggedAccountsController@index')->name('index');
            Route::get('list', 'Admins\AdminTaggedAccountsController@list')->name('list');
        });
    });

    //CMC Routes
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::post('tag/get_admin', 'Admins\AdminCRMController@get_admins')->name('tag.get_admins');
        Route::prefix('request')->name('request.')->group(function () {
            Route::post('add', 'Admins\AdminCRMController@add_request')->name('add');
            //            Route::post('get_request', 'Admins\AdminCRMController@get_request_info')->name('get_request');
            //            Route::post('update', 'Admins\AdminCRMController@update_request')->name('update');
            Route::get('', 'Admins\AdminCRMController@launched')->name('launched');
            Route::get('{id}', 'Admins\AdminCRMController@request_details')->name('details');
            Route::post('edit', 'Admins\AdminCRMController@edit_request')->name('edit');
            Route::post('image_details', 'Admins\AdminCRMController@crm_image_details')->name('image_details');
            Route::post('image_submit', 'Admins\AdminCRMController@crm_image_submit')->name('image_submit');
            Route::post('image_delete', 'Admins\AdminCRMController@crm_image_delete')->name('image_delete');
            Route::post('/lost/claim', 'Admins\AdminCRMController@lost_claim')->name('lost.claim');
            Route::post('request', 'Admins\AdminCRMController@special_request_appvove')->name('special_request_appvove');
            Route::post('request_adjusted', 'Admins\AdminCRMController@special_request_adjusted')->name('special_request_adjusted');

            Route::post('case_nature_remarks', 'Admins\AdminTrackingController@case_nature_remarks')->name('case_nature_remarks');
            Route::post('case_nature_service_remarks', 'Admins\AdminTrackingController@case_nature_service_remarks')->name('case_nature_service_remarks');
            Route::post('case_nature_claim_remarks', 'Admins\AdminTrackingController@case_nature_claim_remarks')->name('case_nature_claim_remarks');
            Route::post('updated_crm_request_nature_types', 'Admins\AdminTrackingController@updated_crm_request_nature_types')->name('updated_crm_request_nature_types');

        });
        Route::prefix('feedback')->name('feedback.')->group(function () {
            Route::post('add', 'Admins\AdminCRMController@add_feedback')->name('add');
        });
        Route::prefix('launched_re_open')->name('launched_re_open.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@launched_re_open_index')->name('index');
            Route::post('list', 'Admins\AdminCRMController@launched_re_open_list')->name('list');
        });
        Route::prefix('in_process')->name('in_process.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@in_process_index')->name('index');
            Route::post('list', 'Admins\AdminCRMController@in_process_list')->name('list');
            Route::post('tag', 'Admins\AdminCRMController@bulk_admin_tag')->name('tag');
            Route::post('un_tag', 'Admins\AdminCRMController@admin_un_tag')->name('un_tag');
            Route::post('special_request_tag', 'Admins\AdminCRMController@special_request_tag')->name('special_request_tag');
        });
        Route::prefix('resolved')->name('resolved.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@resolved_index')->name('index');
            Route::post('list', 'Admins\AdminCRMController@resolved_list')->name('list');
        });
        Route::prefix('closed')->name('closed.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@closed_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@closed_list')->name('list');
        });
        Route::post('assign', 'Admins\AdminCRMController@assign')->name('assign');
        Route::post('close', 'Admins\AdminCRMController@close')->name('close');
        Route::post('valid', 'Admins\AdminCRMController@valid')->name('valid');
        Route::post('bulk_re_open', 'Admins\AdminCRMController@bulk_re_open')->name('bulk_re_open');
        Route::post('invalid', 'Admins\AdminCRMController@invalid')->name('invalid');
        Route::post('tag', 'Admins\AdminCRMController@admin_tag')->name('tag');
        Route::prefix('comment')->name('comment.')->group(function () {
            Route::post('add', 'Admins\AdminCRMController@add_comment')->name('add');
            Route::post('get', 'Admins\AdminCRMController@get_latest_comment')->name('get');
            Route::post('edit', 'Admins\AdminCRMController@edit_comment')->name('edit');
            Route::post('bulk', 'Admins\AdminCRMController@bulk_comment_for_shipper')->name('bulk');
        });
        Route::post('escalation_status', 'Admins\AdminCRMController@escalation_status')->name('escalation_status');
        Route::post('escalate', 'Admins\AdminCRMController@escalate')->name('escalate');
        Route::get('permissions', 'Admins\AdminCRMController@crm_index')->name('permissions');
        Route::get('list', 'Admins\AdminCRMController@crm_list')->name('list');

        Route::prefix('update/{id}')->name('update.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@crm_update_index')->name('index');
            Route::post('', 'Admins\AdminCRMController@crm_update_store')->name('store');
        });
        Route::post('bulk_valid_invalid', 'Admins\AdminCRMController@bulk_valid_invalid')->name('bulk_valid_invalid');
        Route::prefix('claim')->name('claim.')->group(function () {
            Route::get('product_image/{id}', 'Admins\AdminCRMController@product_image')->name('product_image');
            Route::get('invoice_image/{id}', 'Admins\AdminCRMController@invoice_image')->name('invoice_image');
            Route::get('damage_product_image/{id}', 'Admins\AdminCRMController@damage_product_image')->name('damage_product_image');
            Route::get('product_packaging_image/{id}', 'Admins\AdminCRMController@product_packaging_image')->name('product_packaging_image');
            Route::get('actual_product_image/{id}', 'Admins\AdminCRMController@actual_product_image')->name('actual_product_image');
            Route::get('missing_product_image/{id}', 'Admins\AdminCRMController@missing_product_image')->name('missing_product_image');
            Route::get('product_packaging_image_for_content_short/{id}', 'Admins\AdminCRMController@product_packaging_image_for_content_short')->name('product_packaging_image_for_content_short');
            Route::get('actual_product_image_for_content_short/{id}', 'Admins\AdminCRMController@actual_product_image_for_content_short')->name('actual_product_image_for_content_short');
        });
        Route::prefix('consignee_info')->name('consignee_info.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@consignee_info_index')->name('index');
            Route::get('list', 'Admins\AdminCRMController@consignee_info_list')->name('list');
            Route::post('print_air_waybill', 'Admins\AdminCRMController@print_air_waybill')->name('print_air_waybill');
            Route::post('resolve', 'Admins\AdminCRMController@consignee_info_resolve')->name('resolve');
        });
        Route::post('close_reason', 'Admins\AdminCRMController@close_reason')->name('close_reason');

        Route::prefix('bulk_claim')->name('bulk_claim.')->group(function () {
            Route::get('', 'Admins\AdminCRMController@bulk_claim_index')->name('index');
            Route::post('store', 'Admins\AdminCRMController@bulk_claim_submit')->name('submit');
            Route::post('shipment_details', 'Admins\AdminCRMController@bulk_claim_shipment_details')->name('shipment_details');
        });
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('', 'Admins\CRMDashboardController@crm_dashboard_index')->name('index');
            Route::post('list', 'Admins\CRMDashboardController@crm_dashboard_list')->name('list');
            Route::post('card_data', 'Admins\CRMDashboardController@card_data')->name('card_data');
        });

        // bulk resolve
        Route::post('bulk_resolve', 'Admins\AdminCRMController@bulk_resolve')->name('bulk_resolve');
    });

    Route::prefix('intercept')->name('intercept.')->group(function () {
        Route::get('/{row_id}', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_re_book_index')->name('index');
        Route::post('update', 'Admins\AdminInterceptRebookRequestHistoryController@intercept_re_book_update')->name('update');
        Route::post('get_express_centers', 'Admins\AdminInterceptRebookRequestHistoryController@get_express_centers')->name('get_express_centers');
    });

    Route::prefix('resources')->name('resources.')->group(function () {
        Route::get('', 'Admins\AdminResourcesController@index')->name('index');
        Route::get('city_list', 'Admins\AdminResourcesController@get_network_list')->name('city_list');
    });
    Route::prefix('scanning_history')->name('scanning_history.')->group(function () {
        Route::get('', 'Admins\AdminShipmentScanningHistoryController@index')->name('index');
        Route::post('details', 'Admins\AdminShipmentScanningHistoryController@details')->name('details');
        Route::get('details_new', 'Admins\AdminShipmentScanningHistoryController@details_new')->name('details_new');
    });
    Route::prefix('airway_journey')->name('airway_journey.')->group(function () {
        Route::get('', 'AdminAirwayBillJournyController@index')->name('index');
        Route::post('details', 'AdminAirwayBillJournyController@details')->name('details');
    });

    Route::prefix('barcode_generator')->name('barcode_generator.')->group(function () {
        Route::get('', 'BarcodeGeneratorController@index')->name('index');
        Route::get('list', 'BarcodeGeneratorController@list')->name('list');
        Route::post('submit', 'BarcodeGeneratorController@store')->name('submit');
        Route::post('print_barcodes', 'BarcodeGeneratorController@print_barcodes')->name('print_barcodes');

    });



    Route::prefix('coordinates')->name('coordinates.')->group(function () {
        Route::prefix('add')->name('add.')->group(function () {
            Route::get('', 'Admins\CoordinatesController@add_index')->name('index');
            Route::post('submit', 'Admins\CoordinatesController@add_submit')->name('submit');
            Route::post('details', 'Admins\CoordinatesController@shipment_details')->name('shipment_details');
            Route::post('search', 'Admins\CoordinatesController@address_search')->name('search.address');
        });
    });

    Route::prefix('handover')->name('handover.')->group(function () {
        Route::prefix('create')->name('create.')->group(function () {
            // disable the old create handover route
            Route::get('', 'AdminShipmentHandoverController@handover_create_index')->name('index');

            // new handover create screen
            Route::get('new', 'AdminShipmentHandoverController@handover_create_index_new')->name('new_index');

            Route::post('fetch', 'AdminShipmentHandoverController@handover_dropdown_val_fetch_from')->name('fetch');
            Route::post('fetch1', 'AdminShipmentHandoverController@handover_dropdown_val_fetch_to')->name('fetch1');
            Route::post('shipment_details', 'AdminShipmentHandoverController@arrival_bulk_shipment_details')->name('shipment_details');

            // new handover create screen
            Route::post('shipment_details_new', 'AdminShipmentHandoverController@arrival_bulk_shipment_details_new')->name('shipment_details_new');


            Route::post('store', 'AdminShipmentHandoverController@bulk_handover_submit')->name('store');

            // new handover create store
            Route::post('store_new', 'AdminShipmentHandoverController@bulk_handover_submit_new')->name('store_new');

            Route::post('shipments_pieces', 'AdminShipmentHandoverController@add_handover_shipments_pieces')->name('shipments_pieces');
            Route::post('sub_area', 'AdminShipmentHandoverController@sub_area')->name('sub_area');

            // AJAX routes found in new handover create blade file
            Route::get('unique_bag_number', 'AdminShipmentHandoverController@unique_bag_number')->name('unique_bag_number');
            Route::get('check_bag_type', 'AdminShipmentHandoverController@check_bag_type')->name('check_bag_type');
            Route::get('handover_exists', 'AdminShipmentHandoverController@handover_exists')->name('handover_exists');
            Route::get('same_hub_handover_count', 'AdminShipmentHandoverController@same_hub_handover_count')->name('same_hub_handover_count');
            Route::get('permission_to_create_handover', 'AdminShipmentHandoverController@permission_to_create_handover')->name('permission_to_create_handover');
        });
        Route::prefix('receive')->name('receive.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@handover_receive_index')->name('index');
            Route::post('shipment_details', 'AdminShipmentHandoverController@arrival_bulk_shipment_details_receive')->name('shipment_details');
            Route::get('check_handover_bag_receive', 'AdminShipmentHandoverController@check_handover_bag_receive')->name('check_handover_bag_receive');

            // new handover receive screen
            Route::get('new', 'AdminShipmentHandoverController@handover_receive_index_new')->name('new_index');

            // new receive screen shipment details
            Route::post('shipment_details_new', 'AdminShipmentHandoverController@arrival_bulk_shipment_details_receive_new')->name('shipment_details_new');

            Route::post('store', 'AdminShipmentHandoverController@bulk_handover_submit_receive')->name('store');

            // new handover receive submit 
            Route::post('store_new', 'AdminShipmentHandoverController@bulk_handover_submit_receive_new')->name('store_new');

            // AJAX routes found in the new handover receive blade file 
            Route::get('handover_shipment_type', 'AdminShipmentHandoverController@handover_shipment_type')->name('handover_shipment_type');
            Route::get('bag_number_dropdown', 'AdminShipmentHandoverController@bag_number_dropdown')->name('bag_number_dropdown');
            Route::get('check_full_bag', 'AdminShipmentHandoverController@check_full_bag')->name('check_full_bag');
        });
        Route::prefix('list')->name('list.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@handover_list_index')->name('index');
            Route::get('list', 'AdminShipmentHandoverController@handover_list')->name('list');
            Route::post('shipments', 'AdminShipmentHandoverController@handover_shipments_count')->name('shipments');
            Route::put('delivered', 'AdminShipmentHandoverController@handover_shipments_delivered')->name('delivered');
            Route::post('remaining', 'AdminShipmentHandoverController@handover_shipments_remaining')->name('remaining');
            Route::post('print', 'AdminShipmentHandoverController@handover_print')->name('print');
            Route::post('pieces_list', 'AdminShipmentHandoverController@handover_shipments_pieces')->name('pieces_list');
            Route::post('get_user', 'AdminShipmentHandoverController@get_user')->name('get_user');
            Route::post('excess_handover_shipments', 'AdminShipmentHandoverController@excess_handover_shipments_count')->name('excess_handover_shipments');
        });
        Route::prefix('responsibles')->name('responsibles.')->group(function () {
            Route::get('', 'AdminShipmentHandoverController@responsibles_index')->name('index');
            Route::get('list', 'AdminShipmentHandoverController@responsibles_list')->name('list');
            Route::post('add', 'AdminShipmentHandoverController@responsibles_add')->name('add');
            Route::post('status', 'AdminShipmentHandoverController@responsibles_status')->name('status');
            Route::post('details', 'AdminShipmentHandoverController@responsibles_editview')->name('details');
            Route::post('edit', 'AdminShipmentHandoverController@responsibles_edit')->name('edit');
            Route::post('get_sub_area', 'AdminShipmentHandoverController@get_sub_area')->name('get_sub_area');
        });
    });
    Route::prefix('power_bi')->name('power_bi.')->group(function () {
        Route::get('sales', 'Admins\AdminPowerBIController@sales_dashboard_index')->name('sales');
        Route::get('operation', 'Admins\AdminPowerBIController@operation_dashboard_index')->name('operation');
    });
    //Arrival Service Center
    Route::prefix('arrival_service')->name('arrival_service.')->group(function () {
        Route::get('', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_index')->name('index');
        Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_service_details')->name('shipment_details');
        Route::prefix('try_and_buy')->name('try_and_buy.')->group(function () {
            Route::post('item_details', 'Admins\AdminPickupsController@try_and_buy_item_details')->name('item_details');
            Route::post('shipment_details', 'Admins\V2Pickup\V2AdminArrivalServiceController@arrival_try_and_buy_shipment_details')->name('shipment_details');
        });
    });

    Route::prefix('runner')->name('runner.')->group(function () {
        Route::get('', 'Admins\AdminRunnerController@index')->name('index');
        Route::get('list', 'Admins\AdminRunnerController@list')->name('list');
        Route::get('add', 'Admins\AdminRunnerController@runner_details_add_index')->name('add');
        Route::post('add/submit', 'Admins\AdminRunnerController@runner_details_add_submit')->name('add.submit');
        Route::get('edit/{id?}', 'Admins\AdminRunnerController@runner_details_edit_index')->name('edit');
        Route::post('edit/submit', 'Admins\AdminRunnerController@runner_details_edit_submit')->name('edit.submit');
        Route::post('/view_details', 'Admins\AdminRunnerController@runner_details_view')->name('view_details');
        Route::get('in_transit', 'Admins\AdminRunnerController@vehicle_in_transit')->name('intransit');
        Route::get('in_transit/list', 'Admins\AdminRunnerController@vehicle_in_transit_list')->name('intransit.list');
    });

    Route::prefix('open_parcel_history')->name('open_parcel_history.')->group(function () {
        Route::get('', 'Admins\AdminParcelHistoryController@index')->name('index');
        Route::post('info', 'Admins\AdminParcelHistoryController@shipment_get_info')->name('info');
        Route::post('submit', 'Admins\AdminParcelHistoryController@remarks_submit')->name('submit');
        Route::get('list', 'Admins\AdminParcelHistoryController@list')->name('list');
    });
    Route::prefix('trax_directory')->name('trax_directory.')->group(function () {
        Route::get('', 'Admins\AdminTraxDirectory@index')->name('index');
        Route::get('list', 'Admins\AdminTraxDirectory@list')->name('list');
    });

    Route::prefix('international')->name('international.')->group(function () {
        Route::prefix('tracking_upload')->name('tracking_upload.')->group(function () {
            Route::get('', 'Admins\AdminInternationalShipmentsController@tracking_upload_index')->name('index');
            Route::get('list', 'Admins\AdminInternationalShipmentsController@tracking_upload_list')->name('list');
            Route::post('store', 'Admins\AdminInternationalShipmentsController@tracking_upload_store')->name('store');
            Route::get('edit', 'Admins\AdminInternationalShipmentsController@tracking_upload_edit_info')->name('edit');
            Route::post('edit', 'Admins\AdminInternationalShipmentsController@tracking_upload_edit')->name('edit');
            Route::get('logs', 'Admins\AdminInternationalShipmentsController@tracking_logs')->name('logs');
        });
        Route::prefix('shipment_status')->name('shipment_status.')->group(function () {
            Route::get('', 'Admins\AdminInternationalShipmentsController@shipment_status_index')->name('index');
            Route::post('shipment_info', 'Admins\AdminInternationalShipmentsController@get_shipment_info')->name('shipment_info');
            Route::post('update', 'Admins\AdminInternationalShipmentsController@shipment_status_update')->name('update');
            Route::post('updatemodal', 'Admins\AdminInternationalShipmentsController@shipment_status_update_modal')->name('updatemodal');
        });
        Route::prefix('rates')->name('rates.')->group(function () {
            Route::prefix('economy')->name('economy.')->group(function () {
                Route::get('{id}/{view?}', 'Admins\AdminInternationalRatesController@addEconomyRatesView')->name('create');
                Route::post('{id}', 'Admins\AdminInternationalRatesController@addEconomyRatesStore')->name('store');
                Route::post('approve/{id}', 'Admins\AdminInternationalRatesController@addEconomyRatesApprove')->name('approve');
            });
            Route::prefix('view')->name('view.')->group(function () {
                Route::get('{id}', 'Admins\AdminInternationalRatesController@view_rates_index')->name('index');
            });
            Route::prefix('update')->name('update.')->group(function () {
                Route::get('list/{id}', 'Admins\AdminInternationalRatesController@standard_rates_list')->name('list');
                Route::get('{id}', 'Admins\AdminInternationalRatesController@update_rates_index')->name('index');
                Route::post('submit', 'Admins\AdminInternationalRatesController@update_rates_submit')->name('submit');
                Route::post('reject', 'Admins\AdminInternationalRatesController@rejectReasonSubmit')->name('reject');
                Route::post('get_credit', 'Admins\AdminInternationalRatesController@get_credit')->name('get_credit');
                Route::post('credit', 'Admins\AdminInternationalRatesController@credit_update')->name('credit');
            });
        });
        Route::prefix('extra_service_charges')->name('extra_service_charges.')->group(function () {
            Route::get('', 'Admins\AdminInternationalRatesController@extra_service_charges_index')->name('index');
            Route::post('/detail', 'Admins\AdminInternationalRatesController@international_shipment_details')->name('shipment.detail');
            Route::post('submit', 'Admins\AdminInternationalRatesController@extra_service_charges_submit')->name('submit');
        });

        Route::prefix('wholesale')->name('wholesale.')->group(function () {
            Route::prefix('accounts')->name('accounts.')->group(function () {
                Route::get('', 'Admins\InternationalWholesaleController@accounts_index')->name('index');
                Route::get('list', 'Admins\InternationalWholesaleController@accounts_list')->name('list');
                Route::post('store', 'Admins\InternationalWholesaleController@accounts_store')->name('store');
                Route::get('edit', 'Admins\InternationalWholesaleController@accounts_edit_info')->name('edit');
                Route::post('edit', 'Admins\InternationalWholesaleController@accounts_edit')->name('edit');
                Route::post('remove_image', 'Admins\InternationalWholesaleController@accounts_remove_image')->name('remove_image');
                Route::post('change_status', 'Admins\InternationalWholesaleController@accounts_change_status')->name('change_status');
                Route::post('margin', 'Admins\InternationalWholesaleController@accounts_margin')->name('margin');
                Route::post('view_document', 'Admins\InternationalWholesaleController@accounts_view_document')->name('view_document');
            });
            Route::prefix('excel')->name('excel.')->group(function () {
                Route::get('', 'Admins\InternationalWholesaleController@excel_booking_index')->name('index');
                Route::get('list', 'Admins\InternationalWholesaleController@excel_booking_list')->name('list');
                Route::post('store', 'Admins\InternationalWholesaleController@excel_booking_store')->name('store');
                Route::get('edit_info', 'Admins\InternationalWholesaleController@excel_booking_edit_info')->name('edit_info');
                Route::post('edit', 'Admins\InternationalWholesaleController@excel_booking_edit')->name('edit');
                Route::post('cancel', 'Admins\InternationalWholesaleController@excel_booking_cancel')->name('cancel');
            });
            Route::prefix('invoices')->name('invoices.')->group(function () {
                Route::get('', 'Admins\InternationalWholesaleInvoiceController@index')->name('index');
                Route::get('list', 'Admins\InternationalWholesaleInvoiceController@list')->name('list');
                Route::post('resolved', 'Admins\InternationalWholesaleInvoiceController@resolved')->name('resolved');
                Route::get('edit', 'Admins\InternationalWholesaleInvoiceController@edit_info')->name('edit');
                Route::post('edit', 'Admins\InternationalWholesaleInvoiceController@edit')->name('edit');
                Route::get('view_history', 'Admins\InternationalWholesaleInvoiceController@view_history')->name('view_history');
                Route::post('bulk_resolved', 'Admins\InternationalWholesaleInvoiceController@bulk_resolved')->name('bulk_resolved');
                Route::post('general_print', 'Admins\InternationalWholesaleInvoiceController@general_print')->name('general_print');
                Route::post('consolidated_print', 'Admins\InternationalWholesaleInvoiceController@consolidated_print')->name('consolidated_print');
            });
        });
    });

    Route::prefix('telenor')->name('telenor.')->group(function () {
        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@arrival_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@arrival_submit')->name('submit');
        });
        Route::prefix('order_id')->name('order_id.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@order_id_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@order_id_submit')->name('submit');
        });
        Route::prefix('delivery')->name('delivery.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@delivery_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@delivery_submit')->name('submit');
        });
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@return_index')->name('index');
            Route::post('shipment_info', 'Admins\AdminNsaAccountShipmentController@return_shipment_info')->name('shipment_info');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@return_submit')->name('submit');
            Route::get('bulk_return', 'Admins\AdminNsaAccountShipmentController@bulk_return_index')->name('bulk_return');
            Route::post('bulk_return/submit', 'Admins\AdminNsaAccountShipmentController@bulk_return_submit')->name('bulk_return_submit');
        });


        Route::prefix('revert')->name('revert.')->group(function () {
            Route::get('bulk_revert', 'Admins\AdminNsaAccountShipmentController@bulk_revert_index')->name('bulk_revert');
            Route::post('bulk_revert_submit', 'Admins\AdminNsaAccountShipmentController@bulk_revert_submit')->name('bulk_revert_submit');
        });

        Route::prefix('call')->name('call.')->group(function () {
            Route::get('', 'Admins\AdminTelenorController@telenor_response')->name('index');
            Route::get('store', 'Admins\AdminTelenorController@telenor_response_list')->name('list');
        });
    });

    Route::prefix('carrefour')->name('carrefour.')->group(function () {
        Route::prefix('arrival')->name('arrival.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_arrival_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_arrival_submit')->name('submit');
        });
        Route::prefix('delivery')->name('delivery.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_delivery_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_delivery_submit')->name('submit');
        });
        Route::prefix('return')->name('return.')->group(function () {
            Route::get('', 'Admins\AdminNsaAccountShipmentController@carrefour_return_index')->name('index');
            Route::post('store', 'Admins\AdminNsaAccountShipmentController@carrefour_return_submit')->name('submit');
        });
    });

    Route::prefix('team_lead')->name('team_lead.')->group(function () {
        Route::get('', 'Admins\TeamLeadDashboardController@team_lead_index')->name('index');
        Route::get('list', 'Admins\TeamLeadDashboardController@team_lead_list')->name('list');
        Route::post('submit', 'Admins\TeamLeadDashboardController@assign_zone_agent')->name('assign_zone_agent');
        Route::post('deactivate_staff', 'Admins\TeamLeadDashboardController@deactivate_staff')->name('deactivate_staff');
        Route::post('activate_staff', 'Admins\TeamLeadDashboardController@activate_staff')->name('activate_staff');
        Route::post('add_additional_days', 'Admins\TeamLeadDashboardController@add_additional_days')->name('add_additional_days');
        Route::get('delete_additional_days', 'Admins\TeamLeadDashboardController@delete_additional_days')->name('delete_additional_days');
        Route::get('get_updated_day', 'Admins\TeamLeadDashboardController@get_updated_day')->name('get_updated_day');
    });
    
    Route::prefix('assigned_shipment')->name('assigned_shipment.')->group(function () {
        Route::get('', 'Admins\TeamLeadDashboardController@shipment_assign_index')->name('index');
        Route::get('list', 'Admins\TeamLeadDashboardController@shipment_assign_list')->name('list');
    });

    Route::prefix('leads')->name('leads.')->group(function () {
        Route::get('', 'Admins\LeadManagementController@index')->name('index');
        Route::get('list', 'Admins\LeadManagementController@list')->name('list');
        Route::post('lead_reasons', 'Admins\LeadManagementController@lead_reasons')->name('lead_reasons');
        Route::post('add_status', 'Admins\LeadManagementController@add_status')->name('add_status');
        Route::post('add_bulk_status', 'Admins\LeadManagementController@add_bulk_status')->name('add_bulk_status');
        Route::post('tag_sale_person', 'Admins\LeadManagementController@tag_sale_person_forward_lead')->name('tag_sale_person');
        Route::post('call_status', 'Admins\LeadManagementController@call_status_change')->name('call_status');
        Route::post('add_remarks', 'Admins\LeadManagementController@add_remarks')->name('add_remarks');
        Route::get('view_remarks/{id}', 'Admins\LeadManagementController@view_remarks_index')->name('view_remarks');
        Route::post('lead_statistics', 'Admins\LeadManagementController@lead_statistics')->name('lead_statistics');
        Route::post('upload_attachment', 'Admins\LeadManagementController@upload_attachment')->name('upload_attachment');
        Route::get('view_attachment/{id}', 'Admins\LeadManagementController@view_attachment')->name('view_attachment');
        Route::post('info', 'Admins\LeadManagementController@info')->name('info');
        Route::post('edit', 'Admins\LeadManagementController@edit')->name('edit');
        Route::post('add', 'Admins\LeadManagementController@add')->name('add');
        Route::get('edit_service_list', 'Admins\LeadManagementController@edit_service_list')->name('edit_service_list');
        Route::post('/check-lead', 'Admins\LeadManagementController@checkLead')->name('check.lead');
        Route::get('view_logs', 'Admins\LeadManagementController@view_logs')->name('view_logs');

        Route::get('send_mail', 'Admins\LeadManagementController@send_mail')->name('send_mail');

    });

    Route::prefix('pam_leads')->name('pam_leads.')->group(function () {
        Route::get('', 'Admins\LeadManagementController@pam_index')->name('index');
        Route::get('list', 'Admins\LeadManagementController@pam_list')->name('list');
        Route::post('items', 'Admins\LeadManagementController@pam_items')->name('items');
    });

    Route::prefix('retail')->name('retail.')->group(function () {
        Route::get('view_logs', 'Admins\Retail\RetailAdminUserManagementController@view_logs')->name('view_logs');
        Route::prefix('franchise')->name('franchise.')->group(function () {
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@franchise_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@franchise_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@franchise_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@franchise_add')->name('add');
            Route::post('edit', 'Admins\Retail\RetailAdminUserManagementController@franchise_edit')->name('edit');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@franchise_name')->name('name');

            Route::get('{id}/franchise_details_excel_sheet', 'Admins\Retail\RetailAdminUserManagementController@franchise_details_excel_sheet')->name('franchise_details_excel_sheet');

            Route::get('/retail_product_percentage', 'Admins\Retail\RetailAdminUserManagementController@retail_product_percentage')->name('retail_product_percentage');
            Route::get('/retail_product_charges', 'Admins\Retail\RetailAdminUserManagementController@retail_product_charges')->name('retail_product_charges');
            Route::get('/retail_product_attachments', 'Admins\Retail\RetailAdminUserManagementController@retail_product_attachments')->name('retail_product_attachments');

            Route::get('commission', 'Admins\Retail\RetailAdminUserManagementController@franchise_commission_view')->name('franchise_wise_commission');
            Route::get('franchise_commission/ajax', 'Admins\Retail\RetailAdminUserManagementController@franchise_commission_view_ajax_list')->name('commission.list');
            Route::get('user_commission', 'Admins\Retail\RetailAdminUserManagementController@user_commission_view')->name('user_wise_commission');
            Route::get('user_commission/ajax', 'Admins\Retail\RetailAdminUserManagementController@user_commission_view_ajax_list')->name('user_commission.list');

            Route::post('print', 'Admins\Retail\RetailAdminUserManagementController@franchise_commission_invoice_print')->name('franchise_commission_invoice_print');

            Route::post('show_commission', 'Admins\Retail\RetailAdminUserManagementController@show_commission')->name('show_commission');
            Route::post('commission_payment', 'Admins\Retail\RetailAdminUserManagementController@commission_payment')->name('commission_payment');

            Route::get('/cnic_status', 'Admins\Retail\RetailAdminUserManagementController@cnic_status')->name('cnic_status');
        });
        Route::prefix('trax_center')->name('trax_center.')->group(function () {
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@trax_center_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@trax_center_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@trax_center_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@trax_center_add')->name('add');
            Route::post('edit', 'Admins\Retail\RetailAdminUserManagementController@trax_center_edit')->name('edit');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@trax_center_name')->name('name');

            Route::get('/trax_center_edit_attachment', 'Admins\Retail\RetailAdminUserManagementController@trax_center_edit_attachment')->name('trax_center_edit_attachment');

            Route::get('{id}/trax_center_details_excel_sheet', 'Admins\Retail\RetailAdminUserManagementController@trax_center_details_excel_sheet')->name('trax_center_details_excel_sheet');
        });
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('', 'Admins\Retail\RetailAdminUserManagementController@user_index')->name('index');
            Route::get('list', 'Admins\Retail\RetailAdminUserManagementController@user_list')->name('list');
            Route::post('status', 'Admins\Retail\RetailAdminUserManagementController@user_enable_disable')->name('status');
            Route::post('add', 'Admins\Retail\RetailAdminUserManagementController@user_add')->name('add');
            Route::get('edit/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_edit')->name('edit');
            Route::put('update/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_update')->name('update');
            Route::get('name', 'Admins\Retail\RetailAdminUserManagementController@user_name')->name('name');
            Route::get('edit/name/{id}', 'Admins\Retail\RetailAdminUserManagementController@user_edit_name')->name('edit.name');

            Route::get('/retail_user_percentage', 'Admins\Retail\RetailAdminUserManagementController@retail_user_percentage')->name('retail_user_percentage');

            Route::post('print', 'Admins\Retail\RetailAdminUserManagementController@user_commission_invoice_print')->name('user_commission_invoice_print');

            Route::get('/retail_user_attachments', 'Admins\Retail\RetailAdminUserManagementController@retail_user_attachments')->name('retail_user_attachments');

            Route::get('{id}/retail_history', 'Admins\Retail\RetailAdminUserManagementController@retail_history')->name('retail_history');

            Route::post('show_retail_commission', 'Admins\Retail\RetailAdminUserManagementController@show_retail_commission')->name('show_retail_commission');
            Route::post('retail_commission_payment', 'Admins\Retail\RetailAdminUserManagementController@retail_commission_payment')->name('retail_commission_payment');

            Route::get('{id}/retail_user_excel_sheet', 'Admins\Retail\RetailAdminUserManagementController@retail_user_excel_sheet')->name('retail_user_excel_sheet');
            Route::get('{id}/franchise_excel_sheet', 'Admins\Retail\RetailAdminUserManagementController@franchise_excel_sheet')->name('franchise_excel_sheet');

        });

        Route::prefix('international')->name('international.')->group(function () {
            Route::prefix('rates')->name('rates.')->group(function () {
                Route::get('', 'Admins\AdminInternationalRatesController@retail_international_rates_upload_index')->name('index');
                Route::get('list', 'Admins\AdminInternationalRatesController@retail_international_rates_list')->name('list');
                Route::post('excel', 'Admins\AdminInternationalRatesController@retail_international_rates_upload_excel')->name('excel');

                Route::prefix('margin')->name('margin.')->group(function () {
                    Route::get('', 'Admins\AdminInternationalRatesController@retail_international_rates_margin_index')->name('index');
                    Route::post('/submit', 'Admins\AdminInternationalRatesController@retail_international_rates_margin_submit')->name('update');
                    Route::get('/list', 'Admins\AdminInternationalRatesController@retail_international_rates_margin_list')->name('list');
                });
            });

            //International Economy Rates
            Route::prefix('economy-rates')->name('economy_rates.')->group(function () {
                Route::get('', 'Admins\InternationalEconomyStandardRatesController@index')->name('index');
                Route::get('list', 'Admins\InternationalEconomyStandardRatesController@list')->name('list');
                Route::post('excel', 'Admins\InternationalEconomyStandardRatesController@upload_excel')->name('excel');
            });
            //International Zonal Margin Column Mappings
            Route::prefix('zonal-margin-column')->name('zonal_margin_column.')->group(function (){
                Route::get('', 'Admins\GlobalSettingsController@zonal_margin_column_index')->name('index');
                Route::post('submit', 'Admins\GlobalSettingsController@ZoneMarginColumnSubmit')->name('submit');

                // Route::post('zone_margin_column', 'Admins\InternationalEconomyStandardRatesController@zonal_margin_column_index')->name('excel');

            });
        });

        Route::get('add/standard_rates', 'Admins\Retail\RetailAdminUserManagementController@add_standard_rates')->name('add.rates');
        Route::post('standard_rates/submit', 'Admins\Retail\RetailAdminUserManagementController@standard_rates_submit')->name('standard.rates.submit');
        Route::get('standard_rates/edit', 'Admins\Retail\RetailAdminUserManagementController@standard_rates_edit')->name('rates.edit');
        Route::post('update/standard_rates', 'Admins\Retail\RetailAdminUserManagementController@standard_rates_update')->name('standard.rates.update');


        Route::prefix('accounts')->name('accounts.')->group(function () {
            Route::get('', 'Admins\Retail\RetailAdminAccounts@index')->name('index');
            Route::get('/list', 'Admins\Retail\RetailAdminAccounts@list')->name('list');
            Route::post('/slip', 'Admins\Retail\RetailAdminAccounts@retail_slip')->name('retail_slip');
            Route::post('/bank_info', 'Admins\Retail\RetailAdminAccounts@retail_bank_info')->name('bank_info');
            Route::post('/bank_info_update', 'Admins\Retail\RetailAdminAccounts@retail_bank_info_update')->name('bank_info_update');
        });

        Route::prefix('retail_discount_codes')->name('retail_discount_codes.')->group(function () {
            Route::get('', 'Admins\Retail\RetailDiscountCodesController@index')->name('index');
            Route::get('list', 'Admins\Retail\RetailDiscountCodesController@list')->name('list');
            Route::post('store', 'Admins\Retail\RetailDiscountCodesController@add_bulk_retail_discount_codes_store')->name('bulk.store');
        });
    });
    Route::prefix('human_resource')->name('human_resource.')->group(function () {

        Route::get('all_user', 'Admins\AdminHumanResourseController@allusers')->name('allusers');
        Route::get('all_user_ajax', 'Admins\AdminHumanResourseController@all_user_ajax')->name('all_user_ajax');
        Route::get('download_docs', 'Admins\AdminHumanResourseController@download_docs')->name('download_docs');

        Route::prefix('employee_directory')->name('employee_directory.')->group(function () {
            Route::post('rejoin', 'Admins\AdminHumanResourseController@rejoin_employee')->name('rejoin');
            Route::post('pin', 'Admins\AdminHumanResourseController@employee_directory_pin')->name('pin');
            Route::get('', 'Admins\AdminHumanResourseController@employee_directory_index')->name('index');
            Route::post('list', 'Admins\AdminHumanResourseController@employee_directory_list')->name('list');
            Route::post('approve', 'Admins\AdminHumanResourseController@employee_directory_approve')->name('approve');
            Route::post('required_info', 'Admins\AdminHumanResourseController@employee_directory_required_info')->name('required_info');
            Route::post('approve_individual', 'Admins\AdminHumanResourseController@employee_directory_approve_individual')->name('approve_individual');
            Route::post('reject', 'Admins\AdminHumanResourseController@employee_directory_reject')->name('reject');
            Route::get('{employee}/edit', 'Admins\AdminHumanResourseController@employee_directory_edit')->name('edit');
            Route::post('get_designation', 'Admins\AdminHumanResourseController@employee_get_designation')->name('get.designation');
            Route::post('get_cities', 'Admins\AdminHumanResourseController@employee_get_cities')->name('get.cities');
            Route::post('get_routes', 'Admins\AdminHumanResourseController@employee_get_routes')->name('get.routes');
            Route::post('{employee}/profile', 'Admins\AdminHumanResourseController@employee_directory_profile_update')->name('profile.update');
            Route::post('{employee}/medical', 'Admins\AdminHumanResourseController@employee_directory_medical_update')->name('medical.update');
            Route::post('{employee}/bank', 'Admins\AdminHumanResourseController@employee_directory_bank_update')->name('bank.update');
            Route::post('{employee}/reference', 'Admins\AdminHumanResourseController@employee_directory_reference_update')->name('reference.update');
            Route::post('{employee}/education', 'Admins\AdminHumanResourseController@employee_directory_education_update')->name('education.update');
            Route::post('{employee}/employment', 'Admins\AdminHumanResourseController@employee_directory_employment_update')->name('employment.update');
            Route::post('{employee}/attachments', 'Admins\AdminHumanResourseController@employee_directory_attachments_update')->name('attachments.update');
            Route::post('designation_logs', 'Admins\AdminHumanResourseController@designation_change_logs')->name('designation_logs');
            Route::post('employee_log', 'Admins\AdminHumanResourseController@employee_log')->name('employee_log');
            Route::post('get_area', 'Admins\AdminHumanResourseController@get_area')->name('get_area');

            Route::prefix('staff')->name('staff.')->group(function () {
                Route::post('activate', 'Admins\AdminHumanResourseController@employee_directory_make_staff_activate')->name('activate');
                Route::post('deactivate', 'Admins\AdminHumanResourseController@employee_directory_make_staff_deactivate')->name('deactivate');
                Route::post('convert-to-staff', 'Admins\AdminHumanResourseController@convert_intern_to_staff')->name('convert');
                Route::post('convert-to-contractual', 'Admins\AdminHumanResourseController@convert_staff_to_contractual')->name('convert_contractual');
            });
            Route::prefix('rider')->name('rider.')->group(function () {
                Route::post('incentive', 'Admins\AdminHumanResourseController@employee_directory_make_rider_incentive')->name('incentive');
                Route::post('permanent', 'Admins\AdminHumanResourseController@employee_directory_make_rider_permanent')->name('permanent');
                Route::post('blacklist', 'Admins\AdminHumanResourseController@employee_directory_make_rider_blacklist')->name('blacklist');
                Route::post('activate', 'Admins\AdminHumanResourseController@employee_directory_make_rider_activate')->name('activate');
                Route::post('deactivate', 'Admins\AdminHumanResourseController@employee_directory_make_rider_deactivate')->name('deactivate');
                Route::post('update', 'Admins\AdminHumanResourseController@employee_directory_make_rider_update')->name('update');
                Route::post('convert-to-staff', 'Admins\AdminHumanResourseController@convert_rider_to_staff')->name('convert');
            });

            Route::post('get_line_managers', 'Admins\AdminHumanResourseController@get_line_managers')->name('get_line_managers');
            Route::post('update_line_manager', 'Admins\AdminHumanResourseController@update_line_manager')->name('update_line_manager');
        });

        Route::prefix('reporting_location')->name('reporting_location.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@reporting_location_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@reporting_location_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@reporting_location_status')->name('status');
            Route::post('add_location', 'Admins\AdminHumanResourseController@reporting_location_add')->name('add_location');
            Route::post('edit_location', 'Admins\AdminHumanResourseController@reporting_location_edit')->name('edit_location');
        });

        Route::prefix('employee_shifts')->name('employee_shifts.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@employee_shift_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@employee_shift_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@employee_shift_status')->name('status');
            Route::post('add_shift', 'Admins\AdminHumanResourseController@employee_shift_add')->name('add_shift');
            Route::post('edit_shift', 'Admins\AdminHumanResourseController@employee_shift_edit')->name('edit_shift');
        });

        Route::prefix('designation')->name('designation.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@designation_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@designation_list')->name('list');
            Route::post('status', 'Admins\AdminHumanResourseController@designation_status')->name('status');
            Route::post('roles', 'Admins\AdminHumanResourseController@designation_roles')->name('roles');
            Route::post('add', 'Admins\AdminHumanResourseController@designation_add')->name('add');
            Route::post('edit', 'Admins\AdminHumanResourseController@designation_edit')->name('edit');
            Route::post('hub/update', 'Admins\AdminHumanResourseController@designation_hub_update')->name('hub.update');
        });
        Route::prefix('department')->name('department.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@department_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@department_list')->name('list');
            Route::post('add', 'Admins\AdminHumanResourseController@department_add')->name('add');
            Route::post('edit', 'Admins\AdminHumanResourseController@department_edit')->name('edit');
        });

        Route::prefix('leave')->name('leave.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@leave_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@leave_list')->name('list');
            Route::post('leave_request', 'Admins\AdminHumanResourseController@leave_request')->name('leave_request');
            Route::post('edit', 'Admins\AdminHumanResourseController@leave_edit')->name('edit');
            Route::post('hod_approve', 'Admins\AdminHumanResourseController@hod_approve')->name('hod_approve');
            Route::post('approve', 'Admins\AdminHumanResourseController@leave_approve')->name('approve');
            Route::post('reject', 'Admins\AdminHumanResourseController@leave_reject')->name('reject');
        });

        Route::prefix('adjustment')->name('adjustment.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@attendance_adjustment_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@attendance_adjustment_list')->name('list');
        });

        Route::prefix('rider_incentive')->name('rider_incentive.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@rider_incentive_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@rider_incentive_list')->name('list');
        });

        Route::prefix('erf')->name('erf.')->group(function () {
            Route::get('', 'Admins\AdminERFController@index')->name('index');
            Route::get('list', 'Admins\AdminERFController@list')->name('list');
            Route::get('add', 'Admins\AdminERFController@add')->name('add');
            Route::post('submit', 'Admins\AdminERFController@submit_form')->name('submit');
            Route::post('print', 'Admins\AdminERFController@print')->name('print');
            Route::post('file_upload', 'Admins\AdminERFController@file_upload')->name('file_upload');
            Route::post('reject_reason', 'Admins\AdminERFController@reject_reason')->name('reject_reason');
            Route::post('approve', 'Admins\AdminERFController@approve')->name('approve');
            // Route::get('{id}/documents','Admins\AdminERFController@documents')->name('documents');
            Route::post('documents', 'Admins\AdminERFController@documents')->name('documents');
            Route::post('/employee_data', 'Admins\AdminERFController@employee_data')->name('employee_data');
            Route::post('/employee_details', 'Admins\AdminERFController@employee_details')->name('employee_details');
        });

        Route::prefix('fnf')->name('fnf.')->group(function () {
            Route::get('', 'Admins\AdminFnfController@index')->name('index');
            Route::get('list', 'Admins\AdminFnfController@list')->name('list');
            Route::get('/add', 'Admins\AdminFnfController@add')->name('add');
            Route::post('/submit', 'Admins\AdminFnfController@submit')->name('submit');
            Route::post('/employee_data', 'Admins\AdminFnfController@employee_data')->name('employee_data');
            Route::get('{id}/rm', 'Admins\AdminFnfController@reporting_manager_index')->name('rm.index');
            Route::post('rm/submit', 'Admins\AdminFnfController@reporting_manager_submit')->name('rm.submit');
            Route::get('{id}/cs', 'Admins\AdminFnfController@cs_index')->name('cs.index');
            Route::post('cs/submit', 'Admins\AdminFnfController@cs_submit')->name('cs.submit');
            Route::get('{id}/administration', 'Admins\AdminFnfController@administration_index')->name('administration.index');
            Route::post('administration/submit', 'Admins\AdminFnfController@administration_submit')->name('administration.submit');
            Route::get('{id}/it_support', 'Admins\AdminFnfController@it_support_index')->name('it_support.index');
            Route::post('it_support/submit', 'Admins\AdminFnfController@it_support_submit')->name('it_support.submit');
            Route::get('{id}/finance', 'Admins\AdminFnfController@finance_index')->name('finance.index');
            Route::post('finance/submit', 'Admins\AdminFnfController@finance_submit')->name('finance.submit');
            Route::get('{id}/hr', 'Admins\AdminFnfController@hr_index')->name('hr.index');
            Route::post('hr_print', 'Admins\AdminFnfController@hr_print')->name('hr.print');
            Route::post('hr/submit', 'Admins\AdminFnfController@hr_submit')->name('hr.submit');
            Route::post('rm_status_edit', 'Admins\AdminFnfController@rm_status_edit')->name('rm_status_edit');
            Route::post('cs_status_edit', 'Admins\AdminFnfController@cs_status_edit')->name('cs_status_edit');
            Route::post('administration_status_edit', 'Admins\AdminFnfController@administration_status_edit')->name('administration_status_edit');
            Route::post('it_support_status_edit', 'Admins\AdminFnfController@it_support_status_edit')->name('it_support_status_edit');
            Route::post('finance_status_edit', 'Admins\AdminFnfController@finance_status_edit')->name('finance_status_edit');
            Route::get('{id}/hod_approval', 'Admins\AdminFnfController@hod_approval_index')->name('hod_approval_index');
            Route::post('hod_approval/submit', 'Admins\AdminFnfController@hod_approval_submit')->name('hod_approval_submit');
            Route::post('hr_status_edit', 'Admins\AdminFnfController@hr_status_edit')->name('hr_status_edit');
            Route::get('{id}/edit', 'Admins\AdminFnfController@edit_fnf_request')->name('edit_fnf_request');
            Route::post('{/update', 'Admins\AdminFnfController@update_fnf_request')->name('update_fnf_request');
            Route::get('{id}/history', 'Admins\AdminFnfController@fnf_history_index')->name('fnf_history_index');
            Route::get('{id}/history/list', 'Admins\AdminFnfController@status_history_list')->name('status_history_list');
            Route::post('reopen', 'Admins\AdminFnfController@reopen')->name('reopen');
            Route::post('get_reopen_sections', 'Admins\AdminFnfController@get_reopen_sections')->name('get_reopen_sections');
        });

        Route::prefix('payslip')->name('payslip.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@payslip_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@payslip_list')->name('list');
            Route::post('excel', 'Admins\AdminHumanResourseController@payslip_excel_upload')->name('excel');
            Route::post('generate_payslip', 'Admins\AdminHumanResourseController@payslip_print')->name('print');
            Route::get('{id}/payslip_download', 'Admins\AdminHumanResourseController@payslip_download')->name('download');
        });
        Route::prefix('employee_penalty')->name('employee_penalty.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@employee_penalty_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@employee_penalty_list')->name('list');
            Route::get('duplicate', 'Admins\AdminHumanResourseController@employee_penalty_duplicate')->name('duplicate');
            Route::post('reject', 'Admins\AdminHumanResourseController@employee_penalty_reject')->name('reject');
            Route::get('deduction', 'Admins\AdminHumanResourseController@employee_penalty_deduction_list')->name('deduction');
            Route::post('deduction', 'Admins\AdminHumanResourseController@employee_penalty_deduction_store')->name('deduction_store');
        });

        Route::prefix('employee_confirmation')->name('employee_confirmation.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@employee_confirmation_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@employee_confirmation_list')->name('list');
            Route::post('edit', 'Admins\AdminHumanResourseController@employee_confirmation_edit')->name('edit');
            Route::post('reject', 'Admins\AdminHumanResourseController@employee_confirmation_reject')->name('reject');
            Route::post('get_info', 'Admins\AdminHumanResourseController@get_employee_info')->name('get_info');
            Route::post('get_employee_info_name_type', 'Admins\AdminHumanResourseController@get_employee_info_name_type')->name('get_employee_info_name_type');

            Route::post('submit', 'Admins\AdminHumanResourseController@submit_employee_rating')->name('rating');
            Route::post('approve', 'Admins\AdminHumanResourseController@employee_confirmation_approve')->name('approve');
            Route::post('view', 'Admins\AdminHumanResourseController@view_employee_confirmation')->name('view');
        });

        Route::prefix('fuel_allocation')->name('fuel_allocation.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@rider_fuel_allocation_index')->name('index');
            Route::get('list', 'Admins\AdminHumanResourseController@rider_fuel_allocation_list')->name('list');
            Route::post('allocate', 'Admins\AdminHumanResourseController@rider_fuel_allocation_allocate')->name('allocate');
            Route::post('delivery_notes', 'Admins\AdminHumanResourseController@rider_fuel_allocation_delivery_notes')->name('delivery_notes');
        });

        Route::prefix('employee_areas')->name('employee_areas.')->group(function () {
            Route::get('', 'Admins\AdminHumanResourseController@employee_hubs')->name('index');
            Route::post('assign', 'Admins\AdminHumanResourseController@assign_employee_areas')->name('assign');
        });
    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('', 'Admins\Attendance\AdminAttendanceController@admin_attendance_index')->name('index');
        Route::get('list', 'Admins\Attendance\AdminAttendanceController@admin_attendance_list')->name('list');
        Route::post('excel', 'Admins\Attendance\AdminAttendanceController@attendance_excel_upload')->name('excel');
        Route::post('print', 'Admins\Attendance\AdminAttendanceController@attendance_print')->name('print');
        Route::get('/mark', 'Admins\Attendance\AdminAttendanceController@mark_attendance_index')->name('mark');
        Route::post('/mark/submit', 'Admins\Attendance\AdminAttendanceController@mark_attendance_submit')->name('mark.submit');
        Route::get('/mark/list', 'Admins\Attendance\AdminAttendanceController@mark_attendance_list')->name('mark.list');
        Route::prefix('horizontal')->name('horizontal.')->group(function () {
            Route::get('', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_index')->name('index');
            Route::post('table', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_table')->name('table');
            Route::post('list', 'Admins\Attendance\AdminAttendanceController@admin_attendance_horizontal_list')->name('list');
        });
    });
    Route::prefix('leaves')->name('leaves.')->group(function () {
        Route::get('', 'Admins\Attendance\AdminAttendanceController@leaves')->name('leaves');
        Route::get('list', 'Admins\Attendance\AdminAttendanceController@employee_list')->name('list');
        Route::post('leaves_request', 'Admins\Attendance\AdminAttendanceController@leaves_request')->name('leaves_request');
    });

    Route::prefix('rider_delivery_note_otp')->name('rider_delivery_note_otp.')->group(function () {
        Route::get('', 'Admins\UserManagementController@rider_delivery_note_otp_index')->name('index');
        Route::get('list', 'Admins\UserManagementController@rider_delivery_note_otp_list')->name('list');
    });

    Route::prefix('admin_otp')->name('admin_otp.')->group(function () {
        Route::get('', 'Admins\UserManagementController@admin_otp_index')->name('index');
        Route::get('list', 'Admins\UserManagementController@admin_otp_list')->name('list');
        Route::post('update', 'Admins\UserManagementController@admin_otp_update')->name('update');
    });

    Route::prefix('retail_otp')->name('retail_otp.')->group(function () {
        Route::get('', 'Admins\UserManagementController@retail_otp_index')->name('index');
        Route::get('list', 'Admins\UserManagementController@retail_otp_list')->name('list');
//        Route::post('update', 'Admins\UserManagementController@admin_otp_update')->name('update');
    });

    Route::prefix('rider_otp')->name('rider_otp.')->group(function () {
        Route::get('', 'Admins\RiderManagementController@rider_otp_index')->name('index');
        Route::get('list', 'Admins\RiderManagementController@rider_otp_list')->name('list');
        Route::post('update', 'Admins\RiderManagementController@rider_otp_update')->name('update');
    });



    Route::prefix('shipment_otp')->name('shipment_otp.')->group(function () {
        Route::get('', 'Admins\DeliveryController@shipment_otp_index')->name('index');
        Route::get('list', 'Admins\DeliveryController@shipment_otp_list')->name('list');
        Route::post('update', 'Admins\DeliveryController@shipment_otp_update')->name('update');
        Route::post('search_tracking_number', 'Admins\DeliveryController@search_tracking_number')->name('search_tracking_number');
        Route::get('scanning_history', 'Admins\DeliveryController@shipment_otp_scanning_history_index')->name('scanning_history');
        Route::get('shipment_otp_scanning_history_list', 'Admins\DeliveryController@shipment_otp_scanning_history_list')->name('shipment_otp_scanning_history_list');
    });

    Route::prefix('otp_history')->name('otp_history.')->group(function () {
        Route::get('', 'Admins\ShipmentOTPController@otp_history_index')->name('index');
        Route::get('list', 'Admins\ShipmentOTPController@otp_history_list')->name('list');
    });

    Route::prefix('incidence_monitoring')->name('incidence_monitoring.')->group(function () {
        Route::get('/', 'Admins\IncidenceMonitoringController@index')->name('index');
        Route::get('/list', 'Admins\IncidenceMonitoringController@list')->name('list');
        Route::post('/add', 'Admins\IncidenceMonitoringController@add')->name('add');
        Route::post('/get_managers', 'Admins\IncidenceMonitoringController@get_managers')->name('get_managers');
        Route::get('/view/{id}', 'Admins\IncidenceMonitoringController@view_report')->name('view_report');
        Route::prefix('comment')->name('comment.')->group(function () {
            Route::post('/add', 'Admins\IncidenceMonitoringController@add_comment')->name('add');
            Route::post('/get', 'Admins\IncidenceMonitoringController@get_comments')->name('get');
        });
        Route::post('/image_details', 'Admins\IncidenceMonitoringController@image_details')->name('image_details');
        Route::post('/image_submit', 'Admins\IncidenceMonitoringController@image_submit')->name('image_submit');
        Route::post('/update_status', 'Admins\IncidenceMonitoringController@update_status')->name('update_status');
        Route::get('{id}/edit/form', 'Admins\IncidenceMonitoringController@edit')->name('edit');
        Route::put('{id}/update', 'Admins\IncidenceMonitoringController@update')->name('update');
    });

    Route::prefix('qa_evaluation')->name('qa_evaluation.')->group(function () {
        Route::get('/add', 'Admins\QAEvaluationController@add')->name('add');
        Route::post('/handlings', 'Admins\QAEvaluationController@handlings')->name('handlings');
        Route::post('/submit', 'Admins\QAEvaluationController@submit')->name('submit');
        Route::get('', 'Admins\QAEvaluationController@index')->name('index');
        Route::get('list', 'Admins\QAEvaluationController@list')->name('list');
        Route::get('edit/{id}', 'Admins\QAEvaluationController@edit')->name('edit');
        Route::get('view/{id}', 'Admins\QAEvaluationController@view')->name('view');
        Route::get('edit_activities', 'Admins\QAEvaluationController@edit_activities')->name('edit_activities');
        Route::post('/handlings_edit', 'Admins\QAEvaluationController@handlings_edit')->name('handlings_edit');
        Route::post('/update', 'Admins\QAEvaluationController@update')->name('update');
        Route::post('/actvities_data', 'Admins\QAEvaluationController@actvities_data')->name('actvities_data');
        Route::post('update_activities', 'Admins\QAEvaluationController@update_activities')->name('update_activities');
    });
    Route::prefix('qa')->name('qa.')->group(function () {
        Route::prefix('high_alert')->name('high_alert.')->group(function () {
            Route::prefix('shippers')->name('shippers.')->group(function () {
                Route::get('/', 'Admins\HighAlertShipperController@index')->name('index');
                Route::get('/list', 'Admins\HighAlertShipperController@list')->name('list');
                Route::post('/add', 'Admins\HighAlertShipperController@add')->name('add');
                Route::post('/info', 'Admins\HighAlertShipperController@info')->name('info');
                Route::post('/edit', 'Admins\HighAlertShipperController@edit')->name('edit');
                Route::post('/remove', 'Admins\HighAlertShipperController@remove')->name('remove');
            });
        });

        Route::prefix('cx_training')->name('cx_training.')->group(function () {
            Route::get('/', 'Admins\QualityAssuranceController@cx_training_index')->name('index');
            Route::get('/list', 'Admins\QualityAssuranceController@cx_training_list')->name('list');
            Route::post('/add', 'Admins\QualityAssuranceController@cx_training_add')->name('add');
            Route::post('/update_status', 'Admins\QualityAssuranceController@cx_training_update_status')->name('update_status');
        });
    });
    Route::prefix('nps')->name('nps.')->group(function () {
        Route::get('/', 'Admins\NpsController@index')->name('index');
        Route::get('/list', 'Admins\NpsController@list')->name('list');
        Route::get('/add', 'Admins\NpsController@add')->name('add');
        Route::post('/submit', 'Admins\NpsController@submit')->name('submit');
        Route::get('/view_shippers', 'Admins\NpsController@view_shippers')->name('view_shippers');
        Route::get('/view_questions', 'Admins\NpsController@view_questions')->name('view_questions');
        Route::put('status', 'Admins\NpsController@SurveyStatus')->name('status');
        Route::get('edit/{id}', 'Admins\NpsController@survey_edit')->name('edit');
        Route::post('update/{id}', 'Admins\NpsController@survey_update')->name('update');
        Route::get('/response_report', 'Admins\NpsController@response_report')->name('response.report');
        Route::get('/response_report_list', 'Admins\NpsController@response_report_list')->name('response_report_list');
        Route::get('/consolidate_report', 'Admins\NpsController@consolidate_report')->name('consolidate.report');
        Route::get('/consolidate_report_list', 'Admins\NpsController@consolidate_report_list')->name('consolidate_report_list');
        Route::get('/pie_chart', 'Admins\NpsController@pie_chart')->name('pie_chart');
    });

    Route::prefix('vigilance')->name('vigilance.')->group(function () {
        Route::prefix('verification')->name('verification.')->group(function () {
            Route::get('/', 'Admins\VigilanceController@verification_index')->name('index');
            Route::get('/list', 'Admins\VigilanceController@verification_list')->name('list');
            Route::get('delivery_note_info', 'Admins\VigilanceController@delivery_note_info')->name('delivery_note_info');
            //            Route::post('/info', 'Admins\VigilanceController@verification_info')->name('info');
            //  Route::post('/add', 'Admins\VigilanceController@vigilance_note_add')->name('add');
            Route::post('/excess_cns', 'Admins\VigilanceController@verification_excess_cns')->name('excess_cns');
            Route::post('/verify_cns', 'Admins\VigilanceController@verification_verify_cns')->name('verify_cns');
            Route::post('/unverify_cns', 'Admins\VigilanceController@verification_unverify_cns')->name('unverify_cns');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('/', 'Admins\VigilanceController@history_index')->name('index');
                Route::get('/list', 'Admins\VigilanceController@history_list')->name('list');
            });
        });

        Route::prefix('note')->name('note.')->group(function () {
            Route::get('', 'Admins\VigilanceController@vigilance_note_index')->name('index');
            Route::get('list', 'Admins\VigilanceController@vigilance_note_list')->name('list');
            Route::post('info', 'Admins\VigilanceController@vigilance_note_info')->name('info');
            Route::post('add', 'Admins\VigilanceController@vigilance_note_add')->name('add');

            Route::prefix('history')->name('history.')->group(function () {
                Route::get('/', 'Admins\VigilanceController@vigilance_note_history_index')->name('index');
                Route::get('/list', 'Admins\VigilanceController@vigilance_note_history_list')->name('list');
                Route::post('total_shipments', 'Admins\VigilanceController@vigilance_note_total_shipments')->name('total_shipments');
                Route::post('excess_shipments', 'Admins\VigilanceController@vigilance_note_excess_shipments')->name('excess_shipments');
                Route::post('verify_shipments', 'Admins\VigilanceController@vigilance_note_verify_shipments')->name('verify_shipments');

                // unverified shipments
                Route::post('unverified_shipments', 'Admins\VigilanceController@vigilance_note_unverified_shipments')->name('vigilance_note_unverified_shipments');
            });

        });

    });

    Route::prefix('sack_bag')->name('sack_bag.')->group(function () {
        Route::get('', 'Admins\AdminCargoManifestController@sack_bag_index')->name('index');
        Route::get('list', 'Admins\AdminCargoManifestController@sack_bag_list')->name('list');
        Route::post('store', 'Admins\AdminCargoManifestController@add_sack_bag')->name('store');
        Route::post('sack_bag_check', 'Admins\AdminCargoManifestController@sack_bag_no_check')->name('sack_bag_check');
        Route::post('update_sack_bag_status', 'Admins\AdminCargoManifestController@update_sack_bag_status')->name('update_sack_bag_status');
    });

    //New Moudles Routes
    Route::prefix('logistic')->name('logistic.')->group(function(){
        Route::get('', 'Admins\Logistic\AdminLogisticBookingController@index')->name('index');
        Route::get('list', 'Admins\Logistic\AdminLogisticBookingController@list')->name('list');
        Route::get('create', 'Admins\Logistic\AdminLogisticBookingController@create')->name('create');
        Route::post('store','Admins\Logistic\AdminLogisticBookingController@store')->name('store');
        Route::get('edit/{batch_id}/{booking_id}','Admins\Logistic\AdminLogisticBookingController@edit')->name('edit');
        Route::put('update','Admins\Logistic\AdminLogisticBookingController@update')->name('update');

//        Route::get('/shipment/{cn_number}','Admins\Logistic\AdminLogisticBookingController@get_logistic_shipment')->name('shipment');

        Route::post('shipper_info','Admins\Logistic\AdminLogisticBookingController@get_shipper_info')->name('shipper_info');
        Route::post('product_services','Admins\Logistic\AdminLogisticBookingController@get_product_services')->name('product_services');

        //child routes
        Route::prefix('shipment_manifest')->name('shipment_manifest.')->group(function(){
            Route::get('create','Admins\Logistic\ShipmentManifest\AdminShipmentManifestContoller@create')->name('create');
            Route::post('store','Admins\Logistic\ShipmentManifest\AdminShipmentManifestContoller@store')->name('store');
        });

        Route::prefix('rbag_manifest')->name('rbag_manifest.')->group(function(){
            Route::get('create','Admins\Logistic\RbagManifest\AdminRbagManifestController@create')->name('create');
        });
        Route::prefix('transit_manifest')->name('transit_manifest.')->group(function(){
            Route::get('create','Admins\Logistic\TransitManifest\AdminTransitManifestController@create')->name('create');
        });

        Route::prefix('shipper_tagging')->name('shipper_tagging.')->group(function (){
            Route::get('', 'Admins\Logistic\AdminLogisticSetupController@shipper_tagging_index')->name('index');
            Route::get('list', 'Admins\Logistic\AdminLogisticSetupController@shipper_tagging_list')->name('list');
            Route::post('store', 'Admins\Logistic\AdminLogisticSetupController@shipper_tagging_store')->name('store');
            Route::get('edit/{id}', 'Admins\Logistic\AdminLogisticSetupController@shipper_tagging_edit')->name('edit');
            Route::put('update', 'Admins\Logistic\AdminLogisticSetupController@shipper_tagging_update')->name('update');


        });
        Route::prefix('master_product')->name('master_product.')->group(function (){
            Route::get('', 'Admins\Logistic\AdminLogisticSetupController@master_product_index')->name('index');
            Route::get('list', 'Admins\Logistic\AdminLogisticSetupController@master_product_list')->name('list');
            Route::post('store', 'Admins\Logistic\AdminLogisticSetupController@master_product_store')->name('store');
            Route::get('edit/{id}', 'Admins\Logistic\AdminLogisticSetupController@master_product_edit')->name('edit');
            Route::put('update', 'Admins\Logistic\AdminLogisticSetupController@master_product_update')->name('update');

        });

        Route::prefix('product')->name('product.')->group(function (){
            Route::get('', 'Admins\Logistic\AdminLogisticSetupController@product_index')->name('index');
            Route::get('list', 'Admins\Logistic\AdminLogisticSetupController@product_list')->name('list');
            Route::post('store', 'Admins\Logistic\AdminLogisticSetupController@product_store')->name('store');
            Route::get('edit/{id}', 'Admins\Logistic\AdminLogisticSetupController@product_edit')->name('edit');
            Route::put('update', 'Admins\Logistic\AdminLogisticSetupController@product_update')->name('update');


        });

        Route::prefix('service')->name('service.')->group(function (){
            Route::get('', 'Admins\Logistic\AdminLogisticSetupController@service_index')->name('index');
            Route::get('list', 'Admins\Logistic\AdminLogisticSetupController@service_list')->name('list');
            Route::post('store', 'Admins\Logistic\AdminLogisticSetupController@service_store')->name('store');
            Route::get('edit/{id}', 'Admins\Logistic\AdminLogisticSetupController@service_edit')->name('edit');
            Route::put('update', 'Admins\Logistic\AdminLogisticSetupController@service_update')->name('update');

        });


        Route::prefix('cn')->name('cn.')->group(function(){
            Route::prefix('issue_area_store')->name('issue_area_store.')->group(function(){
                Route::get('','Admins\Logistic\AdminCnController@cn_area_store_index')->name('index');
                Route::get('list','Admins\Logistic\AdminCnController@cn_area_store_list')->name('list');
                Route::post('store','Admins\Logistic\AdminCnController@add_cn_area_store')->name('store');
            });

            Route::prefix('receive_admin_store')->name('receive_admin_store.')->group(function(){
                Route::get('','Admins\Logistic\AdminCnController@cn_receive_admin_store_index')->name('index');
                Route::get('list','Admins\Logistic\AdminCnController@cn_receive_admin_store_list')->name('list');
                Route::post('store','Admins\Logistic\AdminCnController@cn_receive_admin_store_store')->name('store');
                Route::get('edit/{id}', 'Admins\Logistic\AdminCnController@cn_receive_admin_store_edit')->name('edit');
                Route::put('update', 'Admins\Logistic\AdminCnController@cn_receive_admin_store_update')->name('update');
            });

            Route::prefix('issue_to_rider')->name('issue_to_rider.')->group(function(){
                Route::get('','Admins\Logistic\AdminCnController@cn_issue_to_rider_index')->name('index');
                Route::get('list','Admins\Logistic\AdminCnController@cn_issue_to_rider_list')->name('list');
                Route::post('store','Admins\Logistic\AdminCnController@cn_issue_to_rider_store')->name('store');
                Route::get('edit/{id}', 'Admins\Logistic\AdminCnController@cn_issue_to_rider_edit')->name('edit');
                Route::put('update', 'Admins\Logistic\AdminCnController@cn_issue_to_rider_update')->name('update');

                Route::get('cn_index/{issue_id}','Admins\Logistic\AdminCnController@rider_cn_index')->name('cn_index');
                Route::get('cn_list/{rider_issue_id}','Admins\Logistic\AdminCnController@rider_cn_list')->name('cn_list');
                Route::post('barcodes_print','Admins\Logistic\AdminCnController@cn_barcodes_print')->name('barcodes_print');


            });



            Route::prefix('child_receive_admin_store')->name('child_receive_admin_store.')->group(function (){
                Route::get('','Admins\Logistic\AdminCnController@cn_child_receive_admin_store_index')->name('index');
                Route::get('list','Admins\Logistic\AdminCnController@cn_child_receive_admin_store_list')->name('list');
                Route::post('store','Admins\Logistic\AdminCnController@cn_child_receive_admin_store_store')->name('store');
                Route::get('edit/{id}', 'Admins\Logistic\AdminCnController@cn_child_receive_admin_store_edit')->name('edit');
                Route::put('update', 'Admins\Logistic\AdminCnController@cn_child_receive_admin_store_update')->name('update');
            });

            Route::prefix('child_issue_to_rider')->name('child_issue_to_rider.')->group(function (){
                Route::get('','Admins\Logistic\AdminCnController@cn_child_issue_to_rider_index')->name('index');
                Route::get('list','Admins\Logistic\AdminCnController@cn_child_issue_to_rider_list')->name('list');
                Route::post('store','Admins\Logistic\AdminCnController@cn_child_issue_to_rider_store')->name('store');
                Route::get('edit/{id}', 'Admins\Logistic\AdminCnController@cn_child_issue_to_rider_edit')->name('edit');
                Route::put('update', 'Admins\Logistic\AdminCnController@cn_child_issue_to_rider_update')->name('update');

                Route::get('cn_index/{issue_id}','Admins\Logistic\AdminCnController@rider_child_cn_index')->name('cn_index');
                Route::get('cn_list/{rider_issue_id}','Admins\Logistic\AdminCnController@rider_child_cn_list')->name('cn_list');
                Route::post('barcodes_print','Admins\Logistic\AdminCnController@cn_barcodes_print')->name('barcodes_print');
            });


        });

        Route::prefix('batch')->name('batch.')->group(function(){
            Route::get('','Admins\Logistic\AdminBatchController@booking_batch_index')->name('index');
            Route::get('list','Admins\Logistic\AdminBatchController@booking_batch_list')->name('list');
            Route::post('assign_batch','Admins\Logistic\AdminBatchController@booking_batch_assign')->name('assign_batch');
            Route::get('batch_bookings/{batch_id}', 'Admins\Logistic\AdminLogisticBookingController@batch_bookings')->name('batch_bookings');
            Route::post('batch_booking_list','Admins\Logistic\AdminLogisticBookingController@batch_booking_list')->name('batch_booking_list');
            Route::post('release_batch','Admins\Logistic\AdminLogisticBookingController@release_batch')->name('release_batch');



//            Route::post('store', 'Admins\Logistic\AdminBatchController@batch_booking_store')->name('store');
//            Route::get('edit/{id}', 'Admins\Logistic\AdminBatchController@batch_booking_edit')->name('edit');
//            Route::put('update', 'Admins\Logistic\AdminBatchController@batch_booking_update')->name('update');
        });


    });
});