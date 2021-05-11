# Zoom Zoom (au.com.agileware.zoomzoom)

This is just another [CiviCRM](https://civicrm.org) extension which integrates with [Zoom](https://zoom.us). Provides the following features:

* CiviRule to create Zoom Webinar from Event and create Zoom Meeting from Event
* CiviRule to update Zoom from Event
* CiviRule to delete Zoom from Event
* CiviRule to add Participant to Zoom
* CiviRule to cancel Participant in Zoom
* Scheduled Job to import Zooms as Events
* Scheduled Job to check Zoom registrations and import as CiviCRM Events and Participants
* Scheduled Job to check Zoom attendances and add CiviCRM Participant and update Participant Status, setting to Attended

The extension is licensed under [AGPL-3.0](LICENSE.txt).

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

## Getting Started

@TODO

## Known Issues

@TODO

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