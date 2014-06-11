<?php

/**
 * Created by PhpStorm.
 * User: chrisbjr
 * Date: 6/11/14
 * Time: 1:36 AM
 */
class IpController extends BaseController
{

    public function detect()
    {

        $validator = Validator::make(Input::all(), array('ipv4' => 'Required|Ip'));

        if ($validator->fails()) {
            $response = array(
                'code'   => 400,
                'status' => 'Bad Request',
                'error'  => $validator->errors()
            );
            return Response::make(json_encode($response), 400, array('Content-type' => 'application/json'));
        }

        $ipAddress = Input::get('ipv4');

        // This creates the Reader object, which should be reused across
        // lookups.
        $reader = new GeoIp2\Database\Reader('GeoLite2-City.mmdb');

        // Replace "city" with the appropriate method for your database, e.g.,
        // "country".
        try {
            $record = $reader->city($ipAddress);
            $data['country'] = $record->country;
            $data['city'] = $record->city;
            $data['location'] = $record->location;

            $response = array(
                'code'   => 200,
                'status' => 'Ok',
                'data'   => $data
            );

            return Response::make(json_encode($response), 200, array('Content-type' => 'application/json'));

        } catch (GeoIp2\Exception\AddressNotFoundException $e) {

            $response = array(
                'code'   => 400,
                'status' => 'Bad Request',
                'error'  => $e->getMessage()
            );

            return Response::make(json_encode($response), 400, array('Content-type' => 'application/json'));
        }
    }

} 