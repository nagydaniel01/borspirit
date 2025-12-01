<?php
    /**
     * Filter the WC Számlázz XML data before it is sent.
     *
     * This filter modifies the invoice header and clears the order number
     * (`rendelesSzam`) in the generated XML.
     *
     * @param object $szamla The Számlázz.hu XML invoice object.
     *
     * @return object Modified invoice object with an empty order number.
     */
    add_filter('wc_szamlazz_xml', function($szamla){
        $szamla->fejlec->rendelesSzam = '';
        return $szamla;
    });