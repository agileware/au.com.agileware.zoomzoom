SET
FOREIGN_KEY_CHECKS=0;

DELETE
FROM civicrm_custom_field
WHERE custom_group_id =
      (
          SELECT id
          FROM civicrm_custom_group
          WHERE name = 'zoom'
      );

DELETE
FROM civicrm_custom_field
WHERE custom_group_id =
      (
          SELECT id
          FROM civicrm_custom_group
          WHERE name = 'zoom_registrant'
      );

DELETE
from civicrm_custom_group
where name like 'zoom';

DELETE
from civicrm_custom_group
where name like 'zoom_registrant';

DROP TABLE IF EXISTS `civicrm_value_zoom`;
DROP TABLE IF EXISTS `civicrm_value_zoom_registrant`;

SET
FOREIGN_KEY_CHECKS=1;
