<?php

namespace VMS;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ClickUpClient
{
    private $client;
    private $apiToken;
    private $baseUrl = 'https://api.clickup.com/api/v2/';

    public function __construct(string $apiToken)
    {
        $this->apiToken = $apiToken;
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => $apiToken,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Get authenticated user
     */
    public function getUser()
    {
        try {
            $response = $this->client->get('user');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get teams (workspaces)
     */
    public function getTeams()
    {
        try {
            $response = $this->client->get('team');
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get spaces in a team
     */
    public function getSpaces(string $teamId)
    {
        try {
            $response = $this->client->get("team/{$teamId}/space");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get lists in a space
     */
    public function getLists(string $spaceId)
    {
        try {
            $response = $this->client->get("space/{$spaceId}/list");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a task
     */
    public function createTask(string $listId, array $taskData)
    {
        try {
            $response = $this->client->post("list/{$listId}/task", [
                'json' => $taskData
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get tasks from a list
     */
    public function getTasks(string $listId, array $params = [])
    {
        try {
            $response = $this->client->get("list/{$listId}/task", [
                'query' => $params
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get a specific task
     */
    public function getTask(string $taskId)
    {
        try {
            $response = $this->client->get("task/{$taskId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Update a task
     */
    public function updateTask(string $taskId, array $taskData)
    {
        try {
            $response = $this->client->put("task/{$taskId}", [
                'json' => $taskData
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete a task
     */
    public function deleteTask(string $taskId)
    {
        try {
            $response = $this->client->delete("task/{$taskId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Add comment to a task
     */
    public function addComment(string $taskId, string $commentText)
    {
        try {
            $response = $this->client->post("task/{$taskId}/comment", [
                'json' => [
                    'comment_text' => $commentText
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a webhook
     */
    public function createWebhook(string $teamId, string $endpoint, array $events)
    {
        try {
            $response = $this->client->post("team/{$teamId}/webhook", [
                'json' => [
                    'endpoint' => $endpoint,
                    'events' => $events
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get webhooks
     */
    public function getWebhooks(string $teamId)
    {
        try {
            $response = $this->client->get("team/{$teamId}/webhook");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create a custom field value
     */
    public function setCustomField(string $taskId, string $fieldId, $value)
    {
        try {
            $response = $this->client->post("task/{$taskId}/field/{$fieldId}", [
                'json' => [
                    'value' => $value
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get list details including available statuses
     */
    public function getList(string $listId)
    {
        try {
            $response = $this->client->get("list/{$listId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Update task status
     */
    public function updateTaskStatus(string $taskId, string $status)
    {
        try {
            $response = $this->client->put("task/{$taskId}", [
                'json' => [
                    'status' => $status
                ]
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
