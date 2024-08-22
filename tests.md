# Tests

The following tests were performed for verification.

*Disclaimer*: This is not an exhaustive list of test cases and scenarios. Tests will be performed as required, and recorded here.

## Test Environment

* CiviCRM version : 5.75.1
* CiviRules version : 3.11

## Test Cases

| Result | Test | Expect |
| --- | --- |
| **PASS**  | Event created of Zoom enabled type | Event is created. A Zoom entity is NOT generated on Event creation. Saving the Event Info form generates a single Zoom entity and saves its details to the Event. |
| **PASS**  | Event created from Event Template of Zoom enabled type | Event is created. A single Zoom Entity is generated, and details saved to the Event. |
| **PASS**  | Event created of NOT Zoom enabled type | Event is created. A Zoom entity is NOT generated. Saving the Event Info form again does NOT generate a Zoom entity. |
| **PASS**  | Event of NOT Zoom enabled type is changed to Zoom enabled type | Event is updated to the Zoom enabled type. A single Zoom Entity is generated, and details saved to the Event. |
| **PASS**  | Event created from Event Template of NOT Zoom enabled type | Event is created. A Zoom entity is NOT generated. Saving the Event Info form again does NOT generate a Zoom entity. |
| **PASS**  | Event of Zoom enabled type is changed to NOT Zoom enabled type | Event is changed to the new type. The associated Zoom entity is unlinked from the Event and deleted from the Zoom account. |
| **PASS**  | Event of Zoom enabled type has title, start and end dates changed | Event is updated. The associated Zoom entity is updated with the same details in the Zoom account - Zoom ID persists. |
