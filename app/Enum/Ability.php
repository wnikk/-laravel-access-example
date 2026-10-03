<?php

namespace App\Enum;

/**
 * Names of rules for people who dislike strings. The package takes a backed enum wherever
 * it takes the name of an ability: addPermission(), can(), allowedTo().
 */
enum Ability: string
{
    case OrdersView = 'orders.view';
    case OrdersApprove = 'orders.approve';
    case CatalogView = 'catalog.view';
}
