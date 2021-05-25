# Zoom Zoom (au.com.agileware.zoomzoom)

This is just another [CiviCRM](https://civicrm.org) extension which integrates with [Zoom](https://zoom.us). Provides the following features:

* CiviRule to create Zoom Webinar from Event and create Zoom Meeting from Event
* CiviRule to update Zoom details
* CiviRule to delete a Zoom
* CiviRule to add an Event Participant to a Zoom 
* CiviRule to delete a Participant from a Zoom
* Scheduled Job to import Zoom Webinars and Zoom Events as CiviCRM Events 
* Scheduled Job to check CiviCRM Events linked to Zoom and import the Zoom registrations, attendees and absentees. Record as CiviCRM Participants and update Participant Status.

The extension is licensed under [AGPL-3.0](LICENSE.txt).

## Getting Started

* Zoom JWT API credentials are required for this CiviCRM Extension. Create a Zoom JWT App in the [Zoom Marketplace](https://marketplace.zoom.us/develop/create).
* Ensure that the Zoom Account used for the Zoom JWT App has permissions to read/write Zooms. For more details read, [Zoom API, JSON Web Tokens (JWT)](https://marketplace.zoom.us/docs/guides/auth/jwt)
* In CiviCRM, go to the Zoom Settings page, Administer > Zoom Settings.
  * Insert the **Zoom JWT API Key** and **Zoom JWT API Secret**.
  * Set the other options on the Zoom Settings page, as required.

* The following **Scheduled Jobs** are provided:
  * **Import Zoom Webinars and Meetings** - Enable this Scheduled Job if you want to create CiviCRM Events from Zooms. Specify a day offset to process _Zooms_ with a start date either in the last X days (by providing a negative number) or from a future date. The default day_offset is -90 which will process Zooms with a start date in the last 90 days.
  * **Import Zoom Registrations, Attendees, Absentees** - Enable this Scheduled Job if you want to create CiviCRM Participant records in CiviCRM from Zoom registrations, attendance and absentees. This job will only process CiviCRM Events which are linked to a Zoom. Specify a day offset to process _Events_ with a start date either in the last X days (by providing a negative number) or from a future date. The default day_offset is -90 which will process Events with a start date in the last 90 days. _Note: As this Scheduled Job checks CiviCRM Events linked to a Zoom, it is a good idea to run this job **after** the **Import Zoom Webinars and Meetings** job_.

## Credits and acknowledgements

Credit to Lighthouse Consulting and Design, Inc for developing [https://github.com/lcdservices/biz.lcdservices.civizoom](https://github.com/lcdservices/biz.lcdservices.civizoom) which was used as the basis for this new extension.
Credit to Veda Consulting for developing [https://github.com/veda-consulting-company/ncn-civi-zoom](https://github.com/veda-consulting-company/ncn-civi-zoom) which was used as reference for the CiviRules implementation.

## Requirements

* PHP v7.4+
* CiviCRM 5.37+

## Installation (Web UI)

Learn more about installing CiviCRM extensions in the [CiviCRM Sysadmin Guide](https://docs.civicrm.org/sysadmin/en/latest/customize/extensions/).

## Installation (CLI, Zip)

Sysadmins and developers may download the `.zip` file for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
cd <extension-dir>
cv dl au.com.agileware.zoomzoom@https://github.com/agileware/au.com.agileware.zoomzoom/archive/master.zip
```

## Installation (CLI, Git)

Sysadmins and developers may clone the [Git](https://en.wikipedia.org/wiki/Git) repo for this extension and
install it with the command-line tool [cv](https://github.com/civicrm/cv).

```bash
git clone https://github.com/agileware/au.com.agileware.zoomzoom.git
cv en zoomzoom
```

# About the Authors

This CiviCRM extension was developed by the team at
[Agileware](https://agileware.com.au).

[Agileware](https://agileware.com.au) provide a range of CiviCRM services
including:

* CiviCRM migration
* CiviCRM integration
* CiviCRM extension development
* CiviCRM support
* CiviCRM hosting
* CiviCRM remote training services

Support your Australian [CiviCRM](https://civicrm.org) developers, [contact
Agileware](https://agileware.com.au/contact) today!

![Agileware](logo/agileware-logo.png)