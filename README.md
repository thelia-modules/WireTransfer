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


Thelia 3 (version 2.2.0+)
-------------------------

À partir de la 2.2.0, le module est **bi-compatible Thelia 2.5 et Thelia 3** (branche twig, Symfony 7.4,
Flexy). L'aiguillage se fait via `WireTransfer::isThelia3()` ; le code Thelia 2 (templates Smarty, hooks)
reste en place et n'est actif que sous Thelia 2. As of 2.2.0 the module is **dual-compatible with Thelia 2.5
and Thelia 3**; the Thelia 2 code path stays in place and is only active under Thelia 2.

### Ce qui change en Thelia 3 / What changes on Thelia 3

- **Back-office** : la page de configuration est rendue en `default-twig` (Twig) via le hook
  `WireTransfer\Hook\Back\ConfigurationHook` (`module.configuration`), template
  `templates/backOffice/default-twig/WireTransfer/module-configuration.html.twig`. En Thelia 2, le rendu
  Smarty (`templates/backOffice/default/module_configuration.html`) est conservé.
- **Front-office (Flexy)** : le hook Smarty `order-placed.additional-payment-info` n'existe plus. Les
  coordonnées bancaires sont désormais fournies par une **fonction Twig** à appeler dans le thème, sur la
  page de confirmation de commande :

    ```twig
    {{ wiretransfer_bank_info(order_id) }}
    ```

  Elle ne rend rien si la commande n'a pas été payée par virement. En Thelia 2, l'affichage reste assuré par
  le hook `order-placed.additional-payment-info` (template Smarty `order-placed.additional-payment-info.html`)
  et la boucle `wiretransfer.get.info`.
- **Routes** : déclarées dans `Config/routing.xml` (chargé par Thelia 2 **et** Thelia 3), sans
  attribut `#[Route]` — sinon Thelia 3 enregistrerait la route en double (chemin inchangé
  `/admin/wiretransfer/configure`).
- **Compatibilité des signatures** : `pay(Order): ?Response`, `install/destroy(?ConnectionInterface)`,
  `ConfigurationForm::getName(): string`, et types de retour sur la boucle — compatibles Thelia 2 et 3.
- **Classe `Database`** résolue à l'exécution (`Thelia\Install\Database` en T2, `Thelia\Core\Install\Database`
  en T3).

### Boucle `wiretransfer.get.info`

Toujours disponible et inchangée (Thelia 2 et Thelia 3) : elle retourne `ACCOUNT_HOLDER_NAME`, `IBAN`, `BIC`,
`MESSAGE` pour un `order_id`. Sous Thelia 3 / Flexy, préférez la fonction Twig `wiretransfer_bank_info()`
ci-dessus, qui encapsule cette logique.