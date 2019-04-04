<?php
/**
 * Created by Nick Harle
 * Date: 23/03/2019
 * Time: 15:29
 *
 * This object contains the string constants used to denote user attributes.
 * These referenced by via html forms as well as keys in numerous associative arrays.
 */

namespace Utils;


abstract class User
{
    const Email = 'email';
    const Username = 'username';
    const Password = 'password';
    const Role = 'role';
    const RoleLead = 'lead';
    const RoleClient = 'client';
    const RoleMember = 'member';
    const RoleAdmin = 'admin';
}