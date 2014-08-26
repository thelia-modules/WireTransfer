
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- wire_tranfer_config
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `wire_transfer_config`;

CREATE TABLE `wire_transfer_config`
(
    `name` VARCHAR(255) NOT NULL,
    `value` VARCHAR(255),
    PRIMARY KEY (`name`)
) ENGINE=InnoDB;


-- ---------------------------------------------------------------------
-- Mail templates for wiretransfer
-- ---------------------------------------------------------------------

-- First, delete existing entries
SET @var := 0;
SELECT @var := `id` FROM `message` WHERE name="order_confirmation_wiretransfer";
DELETE FROM `message` WHERE `id`=@var;
-- Try if ON DELETE constraint isn't set
DELETE FROM `message_i18n` WHERE `id`=@var;

-- Then add new entries
SELECT @max := MAX(`id`) FROM `message`;
SET @max := @max+1;
-- insert message
INSERT INTO `message` (`id`, `name`, `secured`) VALUES
  (@max,
   'order_confirmation_wiretransfer',
   '0'
  );
-- and mail templates
INSERT INTO `message_i18n` (`id`, `locale`, `title`, `subject`, `text_message`, `html_message`) VALUES
  (@max,
   'en_US',
   'Payment transfer confirmation',
   'Payment of order {$order_ref}', 'Dear customer,\r\nThis is a confirmation of the payment of your order {$order_ref} via bank transfert on our shop.\r\nYour invoice is now available in your customer account at {config key="url_site"}\r\nThank you again for your purchase.\r\nThe {config key="store_name"} team.', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">\r\n<head>\r\n    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>\r\n    <title>courriel de confirmation de commande de {config key="url_site"} </title>\r\n    <style type="text/css">\r\n        body {\r\n            font-family: Arial, Helvetica, sans-serif;\r\n            font-size: 100%;\r\n            text-align: center;\r\n        }\r\n        #liencompte {\r\n            margin: 15px 0;\r\n            text-align: center;\r\n            font-size: 10pt;\r\n        }\r\n        #wrapper {\r\n            width: 480pt;\r\n            margin: 0 auto;\r\n        }\r\n        #entete {\r\n            padding-bottom: 20px;\r\n            margin-bottom: 10px;\r\n            border-bottom: 1px dotted #000;\r\n        }\r\n        #logotexte {\r\n            float: left;\r\n            width: 180pt;\r\n            height: 75pt;\r\n            border: 1pt solid #000;\r\n            font-size: 18pt;\r\n            text-align: center;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n<div id="wrapper">\r\n    <div id="entete">\r\n        <h1 id="logotexte">{config key="store_name"}</h1>\r\n        <h2 id="info">The payment of your order is confirmed</h2>\r\n        <h3 id="commande">Reference {$order_ref} </h3>\r\n    </div>\r\n    <p id="liencompte">\r\n        Your invoice is now available in your customer account on\r\n        <a href="{config key="url_site"}">{config key="store_name"}</a>.\r\n    </p>\r\n    <p>Thank you for your order !</p>\r\n    <p>The {config key="store_name"} team.</p>\r\n</div>\r\n</body>\r\n</html>'
  ),
  (@max,
   'fr_FR',
   'Confirmation de virement',
   'Paiement de la commande : {$order_ref}',
   'Cher client,\r\nCe message confirme le paiement par virement bancaire de votre commande numero {$order_ref} sur notre boutique.\r\nVotre facture est maintenant disponible dans votre compte client à l''adresse {config key="url_site"}\r\nMerci encore pour votre achat !\r\nL''équipe {config key="store_name"}', '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\r\n<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="fr">\r\n<head>\r\n    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>\r\n    <title>Confirmation du paiement de votre commande sur {config key="url_site"} </title>\r\n    <style type="text/css">\r\n        body {\r\n            font-family: Arial, Helvetica, sans-serif;\r\n            font-size: 100%;\r\n            text-align: center;\r\n        }\r\n        #liencompte {\r\n            margin: 15px 0;\r\n            text-align: center;\r\n            font-size: 10pt;\r\n        }\r\n        #wrapper {\r\n            width: 480pt;\r\n            margin: 0 auto;\r\n        }\r\n        #entete {\r\n            padding-bottom: 20px;\r\n            margin-bottom: 10px;\r\n            border-bottom: 1px dotted #000;\r\n        }\r\n        #logotexte {\r\n            float: left;\r\n            width: 180pt;\r\n            height: 75pt;\r\n            border: 1pt solid #000;\r\n            font-size: 18pt;\r\n            text-align: center;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n<div id="wrapper">\r\n    <div id="entete">\r\n        <h1 id="logotexte">{config key="store_name"}</h1>\r\n        <h2 id="info">Confirmation du paiement de votre commande</h2>\r\n        <h3 id="commande">N&deg; {$order_ref}</h3>\r\n    </div>\r\n    <p id="liencompte">\r\n        Le suivi de votre commande est disponible dans la rubrique mon compte sur\r\n        <a href="{config key="url_site"}">{config key="url_site"}</a>\r\n    </p>\r\n    <p>Merci pour votre achat !</p>\r\n    <p>L''équipe {config key="store_name"}</p>\r\n</div>\r\n</body>\r\n</html>'
  );


# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
