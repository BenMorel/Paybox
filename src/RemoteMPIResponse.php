<?php

namespace Paybox;

/**
 * A reponse to a 3D Secure authentication.
 */
class RemoteMPIResponse
{
    /**
     * @var string
     */
    private $sessionId = '';

    /**
     * @var string
     */
    private $enrolled = '';

    /**
     * @var string
     */
    private $status = '';

    /**
     * @var string
     */
    private $id3d = '';

    /**
     * RemoteMPIResponse constructor.
     *
     * @param array $data An associative array of data as returned by the authorization servers.
     */
    public function __construct(array $data)
    {
        $map = [
            'IdSession'  => 'sessionId',
            '3DSTATUS'   => 'status',
            '3DENROLLED' => 'enrolled',
            'ID3D'       => 'id3d',
        ];

        foreach ($map as $key => $field) {
            if (isset($data[$key])) {
                $this->{$field} = $data[$key];
            }
        }
    }

    /**
     * Returns the unique session ID that was passed with the authentication request.
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Returns whether the cardholder is enrolled in the 3D Secure scheme.
     *
     * @return bool
     */
    public function isEnrolled()
    {
        return $this->enrolled == 'Y';
    }

    /**
     * Returns whether the 3D Secure authentication was a success.
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->enrolled == 'Y' && $this->status == 'Y';
    }

    /**
     * Returns the ID3D identifier to use for an authorization on the card.
     *
     * @return string
     */
    public function getID3D()
    {
        return $this->id3d;
    }
}
