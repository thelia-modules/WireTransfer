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


Thelia 3 (version 2.3.0+)
-------------------------

À partir de la 2.3.0, le module est **Thelia 3 uniquement** (branche twig, Symfony 7.4, PHP 8.3, Flexy).
Le support Thelia 2 a été retiré : pour Thelia 2.5, utilisez la ligne 2.1. As of 2.3.0 the module is
**Thelia 3 only**; use the 2.1 line for Thelia 2.5.

### Rendu des surfaces / Where each surface lives

- **Back-office** : la page de configuration est rendue via le hook
  `WireTransfer\Hook\Back\ConfigurationHook` (`module.configuration`, déclaré par
  `getSubscribedHooks()`, donc auto-découvert — `Config/config.xml` n'a plus de section `<hooks>`),
  template `templates/backOffice/default-twig/WireTransfer/module-configuration.html.twig`.
- **Front-office** : Thelia 3 a supprimé les hooks Smarty du front **sans remplacement
  fonctionnel** — `ThemeHookInterface` et le tag `thelia.theme_hook` existent, mais rien ne
  consomme le tag et aucune fonction Twig `theme_hook()` n'est enregistrée. Les coordonnées
  bancaires sont donc exposées par une **fonction Twig** :

    ```twig
    {{ wiretransfer_bank_info(order_id) }}
    ```

  Elle ne rend rien si la commande n'a pas été payée par virement. Le markup est construit par
  `WireTransfer\Service\WireTransferBankInfoRenderer`, qui est la **seule barrière
  d'échappement** — le message configuré par le marchand est volontairement rendu brut.
- **Routes** : déclarées dans `Config/routing.xml` **uniquement**, sans attribut `#[Route]` —
  Thelia 3 charge les deux mécanismes et enregistrerait la route en double (chemin inchangé
  `/admin/wiretransfer/configure`).
- **Traductions** : les libellés front vivent désormais dans le domaine racine du module
  (`wiretransfer`, `I18n/en_US.php` et `I18n/fr_FR.php`) et non plus dans `wiretransfer.fo.default`.
  Ce domaine-là n'existait que parce que Thelia le construit en scannant `templates/frontOffice/*` :
  supprimer le dossier Smarty aurait désenregistré le catalogue.

### Boucle `wiretransfer.get.info`

Toujours disponible et inchangée : elle retourne `ACCOUNT_HOLDER_NAME`, `IBAN`, `BIC`, `MESSAGE`
pour un `order_id`. Sous Flexy, préférez la fonction Twig `wiretransfer_bank_info()` ci-dessus, qui
encapsule cette logique — la boucle n'a plus de consommateur dans le module.