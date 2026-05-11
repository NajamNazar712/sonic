# Implementation Plan: Pre-Booking Consignee Validation

## Overview

Add a read-only `POST /api/shipment/validate` endpoint inside the existing `APIToken` middleware + `shipment` prefix group. The endpoint extracts the NSA, blacklist, and BDMK warning logic from `shipment_book` into a new `APIController@shipment_validate` method with no side effects.

## Tasks

- [x] 1. Register the route in routes/api.php
  - Inside the `Route::middleware('APIToken')->group(...)` block, within the existing `Route::prefix('shipment')->name('shipment.')` group, add:
    `Route::post('validate', 'APIController@shipment_validate')->name('validate');`
  - Place it directly after the existing `Route::post('book', ...)` line
  - _Requirements: 1.1, 1.2_

- [x] 2. Implement `APIController@shipment_validate`
  - [x] 2.1 Add the method stub and input validation
    - Add `public function shipment_validate(Request $request)` to `app/Http/Controllers/APIController.php`
    - Replicate the conditional `Validator::extend('phone_number', ...)` logic from `shipment_book` (checking `preg_match('/^(92|03)\d+/', ...)` to choose between Pakistani mobile pattern and digits-only pattern)
    - Define `$rules` with exactly: `consignee_city_id` (required, integer, exists:cities,id), `consignee_name` (required, string, between:1,100), `consignee_address` (required, between:1,255), `consignee_phone_number_1` (required, phone_number)
    - Run `Validator::make($request->all(), $rules)` and return `{status:1, message:"Error(s) in Input", errors:{...}}` on failure
    - _Requirements: 1.3, 1.4, 6.1, 6.2, 6.3_

  - [x] 2.2 Implement the NSA check
    - After validation passes, extract `$consignee_address` from the request
    - Call `NonServiceArea::pluck('name')->toArray()` to get the keyword list
    - Split `$consignee_address` with `preg_split("/[ ,]+/", ...)` and do a case-insensitive token match (same loop as in `shipment_book`)
    - Build `$msg_string` from matched keywords
    - _Requirements: 2.1, 2.2, 2.3_

  - [x] 2.3 Implement the BDMK check
    - Query `BookingDestinationMappingKeyword::join('booking_destination_mappings as bdm', ...)->where('bdm.status', 1)->select([...])->pluck('keyword')->toArray()`
    - Look up `$consignee_city = City::find($consignee_city_id)`
    - Call `$this->check_bdmk($consignee_city->id, $consignee_address, $check_bdmk, $consignee_city->name)`
    - Store `$bdmk_error` from `$bdmk_result['invalid_cities']` if set
    - _Requirements: 4.1, 4.2, 4.3_

  - [x] 2.4 Implement the blacklist check
    - Query `ConsigneeInformation::where('phone', $consignee_phone_number_1)`
    - If found, query `BlacklistedConsignee::where('consignee_information_id', ...)`
    - If blacklisted, fetch `BlacklistSetting::find($blacklist_setting_id)->message`
    - Store result in `$blacklist_message`
    - _Requirements: 3.1, 3.2, 3.3_

  - [x] 2.5 Assemble and return the response
    - Start with `$response = ['status' => 0, 'message' => 'Consignee validated successfully']`
    - If `$msg_string` is not null, set `$response['non_service_area'] = "A Possible Address Anomaly: {$msg_string} Detected! In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222."`
    - If `$blacklist_message` is not null, set `$response['blacklisted_consignee'] = $blacklist_message`
    - If `$bdmk_error` is not empty, set `$response['bdmk_message'] = $bdmk_error`
    - If any warning key is present, change `$response['message']` to `'Consignee validated with warnings'`
    - Return `response()->json($response)`
    - _Requirements: 5.1, 5.2, 5.3, 1.5_

- [x] 3. Checkpoint — verify no side effects and correct responses
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 3.1 Write property test for no side effects (Property 1)
  - **Property 1: No side effects**
  - Send valid requests and assert the `shipments` table row count is unchanged before and after
  - **Validates: Requirements 1.5**

- [ ]* 3.2 Write property test for required field rejection (Property 2)
  - **Property 2: Required field rejection**
  - Generate requests omitting at least one required field; assert `status` 1 and error message
  - **Validates: Requirements 1.3, 1.4**

- [ ]* 3.3 Write property test for NSA keyword detection (Property 3)
  - **Property 3: NSA keyword detection**
  - Generate addresses with/without seeded NSA keywords; assert `non_service_area` present iff keyword matched
  - **Validates: Requirements 2.1, 2.2, 2.3**

- [ ]* 3.4 Write property test for blacklist detection (Property 4)
  - **Property 4: Blacklist detection**
  - Use seeded blacklisted/clean phone numbers; assert `blacklisted_consignee` present iff blacklisted
  - **Validates: Requirements 3.1, 3.2, 3.3**

- [ ]* 3.5 Write property test for BDMK conflict detection (Property 5)
  - **Property 5: BDMK conflict detection**
  - Generate city/address pairs with/without BDMK keywords; assert `bdmk_message` present iff conflict
  - **Validates: Requirements 4.1, 4.2, 4.3**

- [ ]* 3.6 Write property test for status always 0 on valid requests (Property 6)
  - **Property 6: Status always 0**
  - Generate any valid input; assert `status` === 0 regardless of warnings
  - **Validates: Requirements 5.2, 5.3**

- [ ]* 3.7 Write property test for phone number format validation (Property 7)
  - **Property 7: Phone number format validation**
  - Generate phone strings with/without `92`/`03` prefix, valid and invalid; assert correct validation path
  - **Validates: Requirements 6.1, 6.2, 6.3**

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- No migrations or new models are needed — all reads use existing tables
- The `check_bdmk` method is already defined on `APIController` and can be called as `$this->check_bdmk(...)`
- All required model imports (`NonServiceArea`, `ConsigneeInformation`, `BlacklistedConsignee`, `BlacklistSetting`, `BookingDestinationMappingKeyword`, `City`) are already present at the top of `APIController.php`
