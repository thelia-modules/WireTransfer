Wire Tranfer Payment Module
============================

Authors: Thelia <info@thelia.net>, Franck Allimant, <franck@cqfdev.fr>

Contents
--------

fr_FR:

1. Installation
2. Utilisation
3.  Boucle
4.  Intégration

en_US:

1.  Install notes
2.  How to use
3.  Loop
4.  Integration


fr_FR
-----

Ce module permet à vos clients de payer leurs commandes par virement banciare.

Il s'agit d'une amélioration du module PaymentTransfer.

### Installation

Pour installer le module de paiement par virement, téléchargez l'archive et décompressez la dans ```<dossier de thelia>/local/modules```

### Utilisation

Tout d'abord, activez le module dans le Back-Office, onglet "Modules", puis cliquez sur "Configurer" sur la ligne du module.

Via la page de configuration, entrez vos informations bancaires et enregistrez.

### Email de notification de paiement

Un email de notification de paiement est envoyé à vos clients lorsque vous avez bien reçu leur virement, et que leur commande passe à l'état payé. Le contenu de ce mail est configurable dans le back-office -> Configuration -> Template e-mail -> Confirmation de virement

### Boucle

1.  Le type de la boucle est `wiretransfer.get.info`. Cette boucle permet de récupérer les informations bancaires.

    - Arguments:
        1. order_id | obligatoire | id de la commande
    - Sorties:
        1. $ACCOUNT_HOLDER_NAME: nom du titulaire du compte
        2. $IBAN: Numéro IBAN
        3. $BIC: code BIC
        
    - Utilisation:
        ```
        {loop name="wiretransfer.infos" type="wiretransfer.get.info" order_id=$placed_order_id}
           <dt>{intl d='wiretransfer' l="Account holder name"} : </dt>
           <dd>{$ACCOUNT_HOLDER_NAME}</dd>

           <dt>{intl d='wiretransfer' l="IBAN"} : </dt>
           <dd>{$IBAN}</dd>

           <dt>{intl d='wiretransfer' l="BIC code"} : </dt>
           <dd>{$BIC}</dd>
        {/loop}
        ```


### Intégration


Les informations bancaires du commerçant sont affichées sur la page `order-placed.html` du template frontOffice standard,
en utilisant le hook `order-placed.additional-payment-info`.

en_US
-----

This module offers wire transfer payment to your customers.

This is an improvement of the TranferPayment module. 

### Install notes

To install the wire transfer payment module, download the archive and uncompress it in ```<path to thelia>/local/modules```

### Usage

You first need to activate the module in the Back-Office, tab "Modules". Then click on "Configure" on the line of the module.

Using module's the configuration page, enter you Bank account information and save.


### Payment notification e-mail

A notification email is sent to your customers when you've received their wire transfer, and switcher the order to the "paid" status.
 
The content of this e-mail could be configured in the back-office ->  Le contenu de ce mail est configurable dans le back-office -> Configuration -> Mailing templates -> Wire transfer confirmation


### Loop

1.  The loop type is `wiretransfer.get.info`. This loop returns the bank information.

    - Arguments:
        1. order_id | mandatory | id of the order
        
    - Output:
        1. $ACCOUNT_HOLDER_NAME: name of the bank account holder
        2. $IBAN: IBAN number
        3. $BIC: BIC code
        
    - Usage:
        ```
        {loop name="wiretransfer.infos" type="wiretransfer.get.info" order_id=$placed_order_id}
           <dt>{intl d='wiretransfer' l="Account holder name"} : </dt>
           <dd>{$ACCOUNT_HOLDER_NAME}</dd>

           <dt>{intl d='wiretransfer' l="IBAN"} : </dt>
           <dd>{$IBAN}</dd>

           <dt>{intl d='wiretransfer' l="BIC code"} : </dt>
           <dd>{$BIC}</dd>
        {/loop}
        ```


### Integration

The bank account information are displayed in `order-placed.html` file of the default front office template,
using the `order-placed.additional-payment-info` hook.