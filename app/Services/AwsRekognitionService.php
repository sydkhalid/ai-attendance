<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Log;

class AwsRekognitionService
{
    protected $client;

    public function __construct()
    {
        $this->client = new RekognitionClient([
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }


    /**
     * INDEX FACE
     */
    public function indexFace($imageBytes, $studentId)
    {
        try {

            $res = $this->client->indexFaces([
                'CollectionId' => env('AWS_REKOGNITION_COLLECTION'),
                'ExternalImageId' => (string) $studentId,
                'Image' => ['Bytes' => $imageBytes],
                'DetectionAttributes' => ['DEFAULT']
            ]);

            return $res['FaceRecords'][0]['Face']['FaceId'] ?? null;

        } catch (\Exception $e) {
            Log::error("Rekognition Index Error: " . $e->getMessage());
            return null;
        }
    }


    /**
     * SEARCH FACE
     */
    public function searchFaces($imageBytes)
    {
        try {
            $result = $this->client->searchFacesByImage([
                'CollectionId' => env('AWS_REKOGNITION_COLLECTION'),
                'Image' => ['Bytes' => $imageBytes],
                'FaceMatchThreshold' => 80,
                'MaxFaces' => 100 // detect group
            ]);

            return $result['FaceMatches'] ?? [];

        } catch (\Exception $e) {
            Log::error("Rekognition Search Error: " . $e->getMessage());
            return [];
        }
    }


    /**
     * DELETE FACE
     */
    public function deleteFace($faceId)
    {
        try {
            return $this->client->deleteFaces([
                'CollectionId' => env('AWS_REKOGNITION_COLLECTION'),
                'FaceIds' => [$faceId]
            ]);
        } catch (\Exception $e) {
            Log::error("Rekognition Delete Error: " . $e->getMessage());
        }
    }
}
