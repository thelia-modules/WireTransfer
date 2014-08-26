Wire Tranfeer Payment Module
============================

Authors: Thelia <info@thelia.net>, Franck Allimant, <franck@cqfdev.fr>

Summary
-------

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

### Installation

Pour installer le module de paiement par virement, téléchargez l'archive et décompressez la dans ```<dossier de thelia>/local/modules```

### Utilisation

Tout d'abord, activez le module dans le Back-Office, onglet "Modules", puis cliquez sur "Configurer" sur la ligne du module.

Vi la page de configuration, entrez vos informations bancaires et enregistrez.

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


Les informations bancaires du commerçant sont affichées sur la page `order-placed.html` du template frontOffice standard, en utilisant le bloc Smarty `additional-payment-info`.

> Vous devrez peut-être ajouter ce block dans le fichier `order-placed.html` s'il n'est pas déjà présent.

Si vous souhaitez fabriquer une page totalement personnalisée, il vous suffit de la créer dans le fichier `<chemin-du-module>/template/frontOffice/default/transfer-payment-order-placed.html`


en_US
-----

### Install notes

To install the wire transfer payment module, download the archive and uncompress it in ```<path to thelia>/local/modules```

### How to use

You first need to activate the module in the Back-Office, tab "Modules". Then click on "Configure" on the line of the module.

Using module's the configuration page, enter you Bank account information and save.

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

The bank account information are displayed on `order-placed.html` of the default front office template, using the   Smarty block `additional-payment-info`.

> You may have to add this block to your `order-placed.html`file if it is not already present.

If you want to create a fully customized page, create it in the `<module-base-path>/template/frontOffice/default/transfer-payment-order-placed.html` file.
