CREATE TABLE `test`.`js_errors_alerts` (
  `hash` CHAR(32) NOT NULL,
  `date` DATE NOT NULL,
  `error` VARCHAR(10000) NOT NULL,
  PRIMARY KEY (`hash`, `date`));
