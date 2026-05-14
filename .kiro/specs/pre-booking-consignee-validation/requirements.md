# Requirements Document

## Introduction

This feature introduces a lightweight pre-booking consignee validation endpoint (`POST /api/shipment/validate`) that allows shippers to check consignee details for potential issues — non-service area addresses, blacklisted consignees, and booking destination mapping keyword (BDMK) conflicts — before a shipment is created. The endpoint mirrors the warning logic of the existing `POST /api/shipment/book` endpoint but performs no booking, no shipment record creation, and no side effects. The primary use case is bulk pre-booking validation, where a shipper can validate all selected orders and correct or skip problematic ones before submitting.

## Glossary

- **Validation_API**: The new `POST /api/shipment/validate` endpoint described in this document.
- **Shipper**: An authenticated API user (identified via `Authorization` header API token) who books shipments.
- **Consignee**: The recipient of a shipment, identified by name, address, city, and phone number.
- **Non_Service_Area (NSA)**: An address keyword that matches a configured list of areas where delivery may incur additional charges or result in a return.
- **Blacklisted_Consignee**: A consignee whose phone number is recorded in the blacklist, associated with a configurable warning message.
- **BDMK**: Booking Destination Mapping Keyword — a keyword associated with a city mapping that flags address/city mismatches or restricted destinations.
- **APIToken_Middleware**: The existing `APIToken` middleware that authenticates requests via the `Authorization` header and injects `user_id` into the request.
- **Warning**: A non-blocking advisory message returned in the response when a consignee detail triggers a known risk condition.

## Requirements

### Requirement 1: Consignee Validation Endpoint

**User Story:** As a Shipper, I want to validate consignee details before booking, so that I can identify and correct problematic orders before submitting them.

#### Acceptance Criteria

1. THE Validation_API SHALL accept `POST` requests at the path `/api/shipment/validate`.
2. THE Validation_API SHALL require authentication via the `APIToken` middleware, using the `Authorization` request header.
3. WHEN a request is received, THE Validation_API SHALL require the following fields: `consignee_city_id` (integer), `consignee_name` (string, 1–100 characters), `consignee_address` (string, 1–255 characters), and `consignee_phone_number_1` (valid Pakistani phone number format).
4. WHEN a required field is missing or fails validation, THE Validation_API SHALL return a response with `status` 1 and a descriptive `message` identifying the failing field(s).
5. THE Validation_API SHALL NOT create any shipment record, booking log, or any other persistent data as a result of a validation request.

### Requirement 2: Non-Service Area Warning

**User Story:** As a Shipper, I want to know if a consignee address contains a non-service area keyword, so that I can decide whether to proceed with booking.

#### Acceptance Criteria

1. WHEN a validation request is received, THE Validation_API SHALL split the `consignee_address` by spaces and commas and compare each token (case-insensitively) against the configured Non_Service_Area keyword list.
2. WHEN one or more Non_Service_Area keywords are matched in the `consignee_address`, THE Validation_API SHALL include a `non_service_area` field in the response containing the message: `"A Possible Address Anomaly: {matched_keywords} Detected! In case of, Out Of Service Area: Additional charges may apply and Non Service Area: Shipment may be returned. For assistance, Call: 021-38772222."`.
3. WHILE no Non_Service_Area keywords are matched, THE Validation_API SHALL omit the `non_service_area` field from the response.

### Requirement 3: Blacklisted Consignee Warning

**User Story:** As a Shipper, I want to know if a consignee's phone number is blacklisted, so that I can avoid booking shipments to known problematic recipients.

#### Acceptance Criteria

1. WHEN a validation request is received, THE Validation_API SHALL look up the `consignee_phone_number_1` in the `ConsigneeInformation` records and check for an associated `BlacklistedConsignee` entry.
2. WHEN a matching blacklist entry is found and a `BlacklistSetting` message is configured, THE Validation_API SHALL include a `blacklisted_consignee` field in the response containing the configured blacklist message.
3. WHILE no blacklist entry is found for the consignee phone number, THE Validation_API SHALL omit the `blacklisted_consignee` field from the response.

### Requirement 4: BDMK Warning

**User Story:** As a Shipper, I want to know if the consignee's city and address trigger a booking destination mapping keyword conflict, so that I can correct destination mismatches before booking.

#### Acceptance Criteria

1. WHEN a validation request is received, THE Validation_API SHALL evaluate the `consignee_city_id` and `consignee_address` against active Booking Destination Mapping Keywords (BDMK) using the same `check_bdmk` logic applied in the existing `shipment_book` method.
2. WHEN a BDMK conflict is detected, THE Validation_API SHALL include a `bdmk_message` field in the response containing the conflict description.
3. WHILE no BDMK conflict is detected, THE Validation_API SHALL omit the `bdmk_message` field from the response.

### Requirement 5: Clean Validation Response

**User Story:** As a Shipper, I want a clear success response when no issues are found, so that I can confidently proceed with booking.

#### Acceptance Criteria

1. WHEN a validation request is received and no warnings are triggered (no NSA match, no blacklisted consignee, no BDMK conflict), THE Validation_API SHALL return `{"status": 0, "message": "Consignee validated successfully"}`.
2. WHEN a validation request is received and one or more warnings are triggered, THE Validation_API SHALL return `{"status": 0, "message": "Consignee validated with warnings", ...warnings}` where `...warnings` includes only the applicable warning fields.
3. THE Validation_API SHALL always return `status` 0 for any valid request that completes the validation checks, regardless of whether warnings are present.

### Requirement 6: Phone Number Format Validation

**User Story:** As a Shipper, I want the validation endpoint to enforce the same phone number format rules as the booking endpoint, so that I get consistent validation behaviour across both endpoints.

#### Acceptance Criteria

1. WHEN `consignee_phone_number_1` begins with `92` or `03`, THE Validation_API SHALL validate it against the pattern for Pakistani mobile numbers: `+92`, `92`, `0092`, or `03` prefix followed by the correct digit count.
2. WHEN `consignee_phone_number_1` does not begin with `92` or `03`, THE Validation_API SHALL validate that it contains only digits.
3. IF `consignee_phone_number_1` fails format validation, THEN THE Validation_API SHALL return `status` 1 with a descriptive validation error message.
