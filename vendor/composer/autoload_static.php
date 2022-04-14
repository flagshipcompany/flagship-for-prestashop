<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf954dcf915cdff53c356b54d6c6a661
{
    public static $files = array (
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
        'fe62ba7e10580d903cc46d808b5961a4' => __DIR__ . '/..' . '/tightenco/collect/src/Collect/Support/helpers.php',
        'caf31cc6ec7cf2241cb6f12c226c3846' => __DIR__ . '/..' . '/tightenco/collect/src/Collect/Support/alias.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tightenco\\Collect\\' => 18,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\VarDumper\\' => 28,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tightenco\\Collect\\' => 
        array (
            0 => __DIR__ . '/..' . '/tightenco/collect/src/Collect',
        ),
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Flagship\\Apis\\Exceptions\\ApiException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Apis/Exceptions/ApiException.php',
        'Flagship\\Apis\\Requests\\ApiRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Apis/Requests/ApiRequest.php',
        'Flagship\\Shipping\\Collections\\AvailableServicesCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/AvailableServicesCollection.php',
        'Flagship\\Shipping\\Collections\\GetPickupListCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/GetPickupListCollection.php',
        'Flagship\\Shipping\\Collections\\GetShipmentListCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/GetShipmentListCollection.php',
        'Flagship\\Shipping\\Collections\\ManifestListCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/ManifestListCollection.php',
        'Flagship\\Shipping\\Collections\\PackingCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/PackingCollection.php',
        'Flagship\\Shipping\\Collections\\RatesCollection' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Collections/RatesCollection.php',
        'Flagship\\Shipping\\Exceptions\\AssociateShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/AssociateShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\AssociateToDepotException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/AssociateToDepotException.php',
        'Flagship\\Shipping\\Exceptions\\AvailableServicesException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/AvailableServicesException.php',
        'Flagship\\Shipping\\Exceptions\\CancelManifestByIdException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/CancelManifestByIdException.php',
        'Flagship\\Shipping\\Exceptions\\CancelPickupException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/CancelPickupException.php',
        'Flagship\\Shipping\\Exceptions\\CancelShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/CancelShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\ConfirmManifestByIdException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ConfirmManifestByIdException.php',
        'Flagship\\Shipping\\Exceptions\\ConfirmShipmentByIdException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ConfirmShipmentByIdException.php',
        'Flagship\\Shipping\\Exceptions\\ConfirmShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ConfirmShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\CreatePickupException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/CreatePickupException.php',
        'Flagship\\Shipping\\Exceptions\\DepotShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/DepotShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\EditPickupException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/EditPickupException.php',
        'Flagship\\Shipping\\Exceptions\\EditShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/EditShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\FilterException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/FilterException.php',
        'Flagship\\Shipping\\Exceptions\\GetAddressByTokenException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetAddressByTokenException.php',
        'Flagship\\Shipping\\Exceptions\\GetDhlEcommOpenShipmentsException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetDhlEcommOpenShipmentsException.php',
        'Flagship\\Shipping\\Exceptions\\GetDhlEcommRatesException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetDhlEcommRatesException.php',
        'Flagship\\Shipping\\Exceptions\\GetManifestByIdException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetManifestByIdException.php',
        'Flagship\\Shipping\\Exceptions\\GetPickupListException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetPickupListException.php',
        'Flagship\\Shipping\\Exceptions\\GetShipmentByIdException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetShipmentByIdException.php',
        'Flagship\\Shipping\\Exceptions\\GetShipmentListException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/GetShipmentListException.php',
        'Flagship\\Shipping\\Exceptions\\ManifestException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ManifestException.php',
        'Flagship\\Shipping\\Exceptions\\ManifestListException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ManifestListException.php',
        'Flagship\\Shipping\\Exceptions\\PackingException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/PackingException.php',
        'Flagship\\Shipping\\Exceptions\\PrepareShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/PrepareShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\QuoteException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/QuoteException.php',
        'Flagship\\Shipping\\Exceptions\\SmartshipException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/SmartshipException.php',
        'Flagship\\Shipping\\Exceptions\\TrackShipmentException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/TrackShipmentException.php',
        'Flagship\\Shipping\\Exceptions\\ValidateTokenException' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Exceptions/ValidateTokenException.php',
        'Flagship\\Shipping\\Flagship' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Flagship.php',
        'Flagship\\Shipping\\Objects\\Address' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Address.php',
        'Flagship\\Shipping\\Objects\\Manifest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Manifest.php',
        'Flagship\\Shipping\\Objects\\Package' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Package.php',
        'Flagship\\Shipping\\Objects\\Packing' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Packing.php',
        'Flagship\\Shipping\\Objects\\Pickup' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Pickup.php',
        'Flagship\\Shipping\\Objects\\Rate' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Rate.php',
        'Flagship\\Shipping\\Objects\\Service' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Service.php',
        'Flagship\\Shipping\\Objects\\Shipment' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/Shipment.php',
        'Flagship\\Shipping\\Objects\\TrackShipment' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Objects/TrackShipment.php',
        'Flagship\\Shipping\\Requests\\AssociateShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/AssociateShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\AssociateToDepotRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/AssociateToDepotRequest.php',
        'Flagship\\Shipping\\Requests\\AvailableServicesRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/AvailableServicesRequest.php',
        'Flagship\\Shipping\\Requests\\CancelManifestByIdRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/CancelManifestByIdRequest.php',
        'Flagship\\Shipping\\Requests\\CancelPickupRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/CancelPickupRequest.php',
        'Flagship\\Shipping\\Requests\\CancelShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/CancelShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\ConfirmManifestByIdRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/ConfirmManifestByIdRequest.php',
        'Flagship\\Shipping\\Requests\\ConfirmShipmentByIdRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/ConfirmShipmentByIdRequest.php',
        'Flagship\\Shipping\\Requests\\ConfirmShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/ConfirmShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\CreateManifestRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/CreateManifestRequest.php',
        'Flagship\\Shipping\\Requests\\CreatePickupRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/CreatePickupRequest.php',
        'Flagship\\Shipping\\Requests\\EditPickupRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/EditPickupRequest.php',
        'Flagship\\Shipping\\Requests\\EditShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/EditShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\GetAddressByTokenRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetAddressByTokenRequest.php',
        'Flagship\\Shipping\\Requests\\GetDhlEcommOpenShipmentsRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetDhlEcommOpenShipmentsRequest.php',
        'Flagship\\Shipping\\Requests\\GetDhlEcommRatesRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetDhlEcommRatesRequest.php',
        'Flagship\\Shipping\\Requests\\GetManifestByIdRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetManifestByIdRequest.php',
        'Flagship\\Shipping\\Requests\\GetManifestsListRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetManifestsListRequest.php',
        'Flagship\\Shipping\\Requests\\GetPickupListRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetPickupListRequest.php',
        'Flagship\\Shipping\\Requests\\GetShipmentByIdRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetShipmentByIdRequest.php',
        'Flagship\\Shipping\\Requests\\GetShipmentListRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/GetShipmentListRequest.php',
        'Flagship\\Shipping\\Requests\\PackingRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/PackingRequest.php',
        'Flagship\\Shipping\\Requests\\PrepareShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/PrepareShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\QuoteRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/QuoteRequest.php',
        'Flagship\\Shipping\\Requests\\TrackShipmentRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/TrackShipmentRequest.php',
        'Flagship\\Shipping\\Requests\\ValidateTokenRequest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Requests/ValidateTokenRequest.php',
        'Flagship\\Shipping\\Tests\\AssociateShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/AssociateShipmentTests.php',
        'Flagship\\Shipping\\Tests\\AssociateToDepotTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/AssociateToDepotTests.php',
        'Flagship\\Shipping\\Tests\\AvailableServicesCollectionTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/AvailableServicesCollectionTests.php',
        'Flagship\\Shipping\\Tests\\AvailableServicesTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/AvailableServicesTests.php',
        'Flagship\\Shipping\\Tests\\CancelManifestByIdTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/CancelManifestByIdTests.php',
        'Flagship\\Shipping\\Tests\\ConfirmManifestByIdTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/ConfirmManifestByIdTests.php',
        'Flagship\\Shipping\\Tests\\ConfirmShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/ConfirmShipmentTests.php',
        'Flagship\\Shipping\\Tests\\CreateManifestTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/CreateManifestTests.php',
        'Flagship\\Shipping\\Tests\\CreatePickupTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/CreatePickupTests.php',
        'Flagship\\Shipping\\Tests\\EditPickupTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/EditPickupTests.php',
        'Flagship\\Shipping\\Tests\\EditShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/EditShipmentTests.php',
        'Flagship\\Shipping\\Tests\\GetDhlEcommOpenShipmentsTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetDhlEcommOpenShipmentsTests.php',
        'Flagship\\Shipping\\Tests\\GetDhlEcommRatesTest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetDhlEcommRatesTests.php',
        'Flagship\\Shipping\\Tests\\GetManifestByIdTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetManifestByIdTests.php',
        'Flagship\\Shipping\\Tests\\GetManifestsListTest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetManifestsListTests.php',
        'Flagship\\Shipping\\Tests\\GetPickupListTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetPickupListTests.php',
        'Flagship\\Shipping\\Tests\\GetPickupListsCollectionTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetPickupListsCollectionTests.php',
        'Flagship\\Shipping\\Tests\\GetShipmentByIdTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetShipmentByIdTests.php',
        'Flagship\\Shipping\\Tests\\GetShipmentListTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetShipmentListTests.php',
        'Flagship\\Shipping\\Tests\\GetShipmentListsCollectionTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/GetShipmentListsCollectionTests.php',
        'Flagship\\Shipping\\Tests\\ManifestTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/ManifestTests.php',
        'Flagship\\Shipping\\Tests\\PackingTest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/PackingTests.php',
        'Flagship\\Shipping\\Tests\\PickupTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/PickupTests.php',
        'Flagship\\Shipping\\Tests\\PrepareShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/PrepareShipmentTests.php',
        'Flagship\\Shipping\\Tests\\QuoteTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/QuoteTests.php',
        'Flagship\\Shipping\\Tests\\RateTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/RateTests.php',
        'Flagship\\Shipping\\Tests\\RatesCollectionTest' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/RatesCollectionTests.php',
        'Flagship\\Shipping\\Tests\\ShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/ShipmentTests.php',
        'Flagship\\Shipping\\Tests\\TrackShipmentTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/TrackShipmentTests.php',
        'PhpToken' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/PhpToken.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValidateTokenTests' => __DIR__ . '/..' . '/flagshipcompany/flagship-api-sdk/Shipping/Tests/ValidateTokenTests.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf954dcf915cdff53c356b54d6c6a661::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf954dcf915cdff53c356b54d6c6a661::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitcf954dcf915cdff53c356b54d6c6a661::$classMap;

        }, null, ClassLoader::class);
    }
}