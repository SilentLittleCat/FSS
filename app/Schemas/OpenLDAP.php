<?php

namespace App\Schemas;

use Adldap\Schemas\ActiveDirectory;

class OpenLDAP extends ActiveDirectory
{
    /**
     * {@inheritdoc}
     */
    public function objectCategory()
    {
        return 'objectclass';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassPerson()
    {
        return 'inetorgperson';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassOu()
    {
        return 'groupofuniquenames';
    }

    /**
     * {@inheritdoc}
     */
    public function objectClassGroup()
    {
        return 'groupofnames';
    }

    /**
     * {@inheritdoc}
     */
    public function objectGuid()
    {
        return 'entryuuid';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedName()
    {
        return 'dn';
    }

    /**
     * {@inheritdoc}
     */
    public function distinguishedNameSubKey()
    {
        //
    }

    public function homeAddress()
    {
        return 'homedirectory';
    }

    public function objectClassUser()
    {
        return 'inetorgperson';
    }

    public function user()
    {
        return 'inetorgperson';
    }

    public function objectCategoryGroup()
    {
        return 'groupofuniquenames';
    }
}
