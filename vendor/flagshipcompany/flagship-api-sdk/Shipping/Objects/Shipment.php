<?php

namespace Flagship\Shipping\Objects;

use Flagship\Shipping\Objects\Package;


class Shipment{


        public function __construct(\stdclass $shipment){
            $this->shipment = $shipment;
        }

        public function getId() : int {
            if(property_exists($this->shipment, 'shipment_id')){
                return $this->shipment->shipment_id;
            }
            return $this->shipment->id;
        }


        public function getTrackingNumber()  {
            return property_exists($this->shipment, 'tracking_number') ? $this->shipment->tracking_number : NULL ;
        }


        public function getStatus()  {
            return property_exists($this->shipment, 'status') ? $this->shipment->status : NULL;
        }


        public function getPickupId()  {
            return property_exists($this->shipment, 'pickup_id') ? $this->shipment->pickup_id : NULL ;
        }


        public function getSenderCompany()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->name : NULL ;
        }


        public function getSenderName()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->attn : NULL;
        }


        public function getSenderAddress()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->address : NULL;
        }


        public function getSenderSuite()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->suite : NULL;
        }


        public function getSenderDepartment()  {

            return property_exists($this->shipment, 'from') ? ((property_exists($this->shipment->from, 'department')) ? $this->shipment->from->department : NULL) : NULL;
        }


        public function getSenderCity()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->city : NULL;
        }


        public function getSenderCountry()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->country : NULL;
        }


        public function getSenderState()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->state : NULL;
        }


        public function getSenderPostalCode()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->postal_code : NULL;
        }


        public function getSenderPhone()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->phone : NULL;
        }


        public function getSenderPhoneExt()  {
            return property_exists($this->shipment, 'from') ? $this->shipment->from->phone_ext : NULL;
        }


        public function getSenderDetails()  {

            $sender = property_exists($this->shipment, 'from') ?json_decode(json_encode($this->shipment->from),TRUE) : NULL ;
            return $sender;
        }


        public function getReceiverCompany()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->name : NULL;
        }


        public function getReceiverName()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->attn : NULL;
        }


        public function getReceiverAddress()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->address : NULL;
        }


        public function getReceiverSuite()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->suite : NULL;
        }


        public function getReceiverDepartment()  {
            return property_exists($this->shipment, 'to') ? (property_exists($this->shipment->to, 'department') ? $this->shipment->to->department  : NULL ) : NULL;
        }


        public function IsReceiverCommercial()  {
            return property_exists($this->shipment, 'to') ? ($this->shipment->to->is_commercial ?  TRUE : FALSE) : NULL;
        }


        public function getReceiverCity()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->city : NULL;
        }


        public function getReceiverCountry()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->country : NULL;
        }


        public function getReceiverState()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->state : NULL;
        }


        public function getReceiverPostalCode()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->postal_code : NULL;
        }


        public function getReceiverPhone()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->phone : NULL;
        }


        public function getReceiverPhoneExt()  {
            return property_exists($this->shipment, 'to') ? $this->shipment->to->phone_ext : NULL;
        }


        public function getReceiverDetails()  {
            $receiver = property_exists($this->shipment, 'to') ?json_decode(json_encode($this->shipment->to),TRUE) : NULL;

            return $receiver;
        }

        public function getReference()  {
            return property_exists($this->shipment->options, 'reference') ? $this->shipment->options->reference : NULL ;
        }


        public function getDriverInstructions()  {

            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'driver_instructions') ? $this->shipment->options->driver_instructions : NULL) : NULL;

        }

         public function isSignatureRequired()  {
             return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'signature_required')? ($this->shipment->options->signature_required ? TRUE : FALSE) : NULL ) : NULL;
         }


        public function getShippingDate() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'shipping_date') ?$this->shipment->options->shipping_date : NULL) : NULL ;
        }


        public function getTrackingEmails() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'tracking_emails') ?  $this->shipment->options->tracking_emails : NULL) : NULL;
        }


        public function getInsuranceValue()
         {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'insurance') ? $this->shipment->options->insurance->value : NULL) : NULL;
        }

        public function getInsuranceDescription() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'insurance') ? $this->shipment->options->insurance->description : NULL) : NULL;
        }


        public function getCodMethod() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->method :NULL) : NULL;
        }


        public function getCodPayableTo() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->payable_to :NULL) : NULL ;
        }


        public function getCodReceiverPhone() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->receiver_phone :NULL) : NULL;
        }


        public function getCodAmount()  {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->amount :NULL) : NULL;
        }


        public function getCodCurrency() {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'cod') ? $this->shipment->options->cod->currency :NULL) : NULL;
        }


        public function IsSaturdayDelivery()  {
            return property_exists($this->shipment, 'options') ? (property_exists($this->shipment->options, 'saturday_delivery') ? ( $this->shipment->options->saturday_delivery ? TRUE : FALSE ) : NULL ) : NULL;
        }


        public function getCourierCode() {
            return $this->shipment->service->courier_code;
        }

        public function getCourierDescription() {
            return $this->shipment->service->courier_desc;
        }

        public function getCourierName() {
            return $this->shipment->service->courier_name;
        }

        public function getEstimatedDeliveryDate() {
            return $this->shipment->service->estimated_delivery_date;
        }

        public function getPackages() {
            return new Package(json_decode(json_encode($this->shipment->packages),TRUE));
        }

        public function getPackageContent() {
            if(is_array($this->shipment->packages)){
                return NULL;
            }
            return $this->shipment->packages->content;
        }


        public function getPackageUnits() {
            if(is_array($this->shipment->packages)){
                return NULL;
            }
            return $this->shipment->packages->units;
        }


        public function getPackageType() {
            if(is_array($this->shipment->packages)){
                return NULL;
            }
             return $this->shipment->packages->type;
        }


        public function getItemsDetails() : array {
            $items = [];
            if(is_object($this->shipment->packages)){
                return $this->shipment->packages->items;
            }

            foreach ($this->shipment->packages as $item) {
                $items[] = $item;
            }
            return $items;
        }


        public function getSubtotal()
         {
            if(property_exists($this->shipment, 'subtotal')){
                return $this->shipment->subtotal;
            }

            if(property_exists($this->shipment,'price')){
                return $this->shipment->price->subtotal;
            }
            return NULL;
        }


        public function getTotal()
         {
            if(property_exists($this->shipment, 'total')){
                return $this->shipment->total;
            }

            if(property_exists($this->shipment,'price')){
                return $this->shipment->price->total;
            }
            return NULL;
        }


        public function getTaxesDetails() {
            if(property_exists($this->shipment, 'taxes')){
                return json_decode(json_encode($this->shipment->taxes),TRUE);
            }

            if(property_exists($this->shipment,'price')){
                return json_decode(json_encode($this->shipment->price->taxes),TRUE);
            }
            return NULL;
        }

        public function getTaxesTotal()  {
                $sum = 0.00;
                $taxes = property_exists($this->shipment, 'taxes') ? $this->shipment->taxes : $this->shipment->price->taxes;

                if(is_null($taxes)){
                    return $sum;
                }

                foreach ($taxes as $tax) {
                    $sum += $tax;

                }

                return $sum;
        }

        public function getCharges() {
            return json_decode(json_encode($this->shipment->price->charges),TRUE);
        }

        public function getAdjustments() {
            $adjustments = property_exists($this->shipment, 'adjustments') ? $this->shipment->adjustments : $this->shipment->price->adjustments;
            return $adjustments;
        }

        public function getDebits() {
            $debits = property_exists($this->shipment, 'debits') ? $this->shipment->debits : $this->shipment->price->debits;
            return $debits;
        }


    public function getLabel() {

        $label =  property_exists($this->shipment, 'documents') ? $this->shipment->documents->regular_label : NULL;
        $label = property_exists($this->shipment, 'labels') ? $this->shipment->labels->regular : $label;
        return $label;
    }

    public function getThermalLabel() {

        $thermalLabel =  property_exists($this->shipment, 'documents') ? $this->shipment->documents->thermal_label : NULL;
        $thermalLabel = property_exists($this->shipment, 'labels') ? $this->shipment->labels->thermal : $thermalLabel;
        return $thermalLabel;
    }

    public function getCommercialInvoice()  {

        return property_exists($this->shipment, 'documents') ? ( property_exists($this->shipment->documents, 'commercial_invoice') ? $this->shipment->documents->commercial_invoice : NULL) : NULL;
    }

    public function getTransitDetails()  {

        return property_exists($this->shipment,'transit_details') ? $this->shipment->transit_details : NULL;
    }

    public function isDocumentsOnly()  {

            return property_exists($this->shipment, 'documents_only') ? ($this->shipment->documents_only ? TRUE : FALSE ) : NULL;
        }


    public function getFlagshipCode() {
        return property_exists($this->shipment->service,'flagship_code') ?$this->shipment->service->flagship_code : NULL;
    }

    public function getTransitTime() {
        return property_exists($this->shipment->service, 'transit_time') ? $this->shipment->service->transit_time : NULL ;
    }


}
