# Tests

The following tests were performed for verification.

*Disclaimer*: This is not an exhaustive list of test cases and scenarios. Tests will be performed as required, and recorded here.

## Latest Test Environment

* CiviCRM version : 5.75.1

## Assumptions

* CiviRules for creating Zooms are configured with `Event is changed` trigger only (not using `Event is added`)

## Test Cases

| Result | Test | Expect |
| --- | --- |
| **PASS**  | Event created of Zoom enabled type | Event is created. A Zoom entity is NOT generated on Event creation. Saving the Event Info form generates a single Zoom entity and saves its details to the Event. |
| **PASS**  | Event created from Event Template of Zoom enabled type | Event is created. A single Zoom Entity is generated, and details saved to the Event. |
| **PASS**  | Event created of NOT Zoom enabled type | Event is created. A Zoom entity is NOT generated. Saving the Event Info form again does NOT generate a Zoom entity. |
| **PASS**  | Event of NOT Zoom enabled type is changed to Zoom enabled type | Event is updated to the Zoom enabled type. A single Zoom Entity is generated, and details saved to the Event. |
| **PASS**  | Event created from Event Template of Zoom NOT enabled type | Event is created. A single Zoom Entity is generated, and details saved to the Event. |
