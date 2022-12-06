<?php

namespace App\Policies;

use App\Models\User;
use App\Models\offer\offer;
use App\Models\order\order;
use App\Models\users\customer;
use App\Models\users\serviceProvider;
use Illuminate\Auth\Access\HandlesAuthorization;

class offerPolicy
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
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, offer $offer)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(serviceProvider $user , order $order)
    {
        return $order->category_id = $user->catgeory_id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, offer $offer)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, offer $offer)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, offer $offer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\offer\offer  $offer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, offer $offer)
    {
        //
    }


    public function acceptAndHold( customer $customer , offer $offer){

        return ($offer->status == 1)&&($offer->order->client_id == $customer->id)&&($offer->order->status == 1) ;
      }

    public function sendUpdate(serviceProvider $sp,offer $offer){
     return ($offer->status == 2)&&($offer->user_id == $sp->id);
    }
}
