<?php

namespace App\Policies;

use App\Http\Middleware\customer;
use App\Models\User;
use App\Models\order\order;
use App\Models\users\customer as UsersCustomer;
use Illuminate\Auth\Access\HandlesAuthorization;

class orderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, order $order)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, order $order)
    {

    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, order $order)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, order $order)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\order\order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, order $order)
    {
        //
    }

    public function show_offers(UsersCustomer $customer , $order ){
        return  $order->client_id == $customer->id && $order->status == 1;
    }
    public function perform_updates(UsersCustomer $customer , $order ){
        return  $order->client_id == $customer->id && $order->status == 2;
    }
    public function update_delete_order(UsersCustomer $customer , order $order){

        return $order->offers->count() == 0 && $order->status == 1 && $order->client_id == $customer->id;

    }
}
