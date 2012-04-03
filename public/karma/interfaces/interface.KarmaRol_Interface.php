<?php
/**
 * @author Alayn Gortazar <alayn+karma@irontec.com>
 * @copyright Irontec S.L. 2011
 * @see The GNU Public License (GPL)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

interface KarmaRol_Interface
{
    /**
     * Devuelve el identificador único del usuario (clave)
     *  Id en MySQL
     *  dn en LDAP
     *  etc...
     * @return mixed
     */
    public function getId();
    public function __toString();
}