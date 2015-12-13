<?php

namespace Paybox;

/**
 * A reponse to a 3D Secure authentication.
 */
class RemoteMPIResponse
{
    const STATUS_UNKNOWN               = 0;
    const STATUS_NOT_ENROLLED          = 1;
    const STATUS_ENROLLED_AUTH_SUCCESS = 2;
    const STATUS_ENROLLED_AUTH_FAILURE = 3;

    /**
     * @var int
     */
    private $status = self::STATUS_UNKNOWN;

    /**
     * @var string
     */
    private $sessionId = '';

    /**
     * @var string
     */
    private $id3d = '';

    /**
     * RemoteMPIResponse constructor.
     *
     * @param array $data An associative array of data as returned by the authentication servers.
     */
    public function __construct(array $data)
    {
        if (isset($data['IdSession'])) {
            $this->sessionId = $data['IdSession'];
        }

        if (isset($data['ID3D'])) {
            $this->id3d = $data['ID3D'];
        }

        if (! isset($data['3DENROLLED'])) {
            return;
        }

        switch ($data['3DENROLLED']) {
            case 'Y':
                break;

            case 'N':
                $this->status = self::STATUS_NOT_ENROLLED;
                return;

            default:
                return;
        }

        if (isset($data['3DSTATUS']) && $data['3DSTATUS'] == 'Y') {
            $this->status = self::STATUS_ENROLLED_AUTH_SUCCESS;
        } else {
            $this->status = self::STATUS_ENROLLED_AUTH_FAILURE;
        }
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
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
     * Returns the ID3D identifier to use for an authorization on the card.
     *
     * @return string
     */
    public function getID3D()
    {
        return $this->id3d;
    }
}
