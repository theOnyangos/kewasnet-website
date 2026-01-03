<?php

namespace App\Services;

use CodeIgniter\HTTP\ResponseInterface;
use InvalidArgumentException;

class DataTableService
{
    /**
     * @var \CodeIgniter\HTTP\Request
     */
    protected $request;

    /**
     * @var \CodeIgniter\HTTP\Response
     */
    protected $response;

    public function __construct()
    {
        $this->request = service('request');
        $this->response = service('response');
    }

    /**
     * Common handler for DataTable requests
     * 
     * @param mixed $model The model instance
     * @param array $columns Allowed columns for ordering
     * @param string $dataMethod Method to get paginated data
     * @param string $countMethod Method to get total count
     * @param callable|null $dataFormatter Optional data formatter function
     * @param array $additionalParams Additional parameters to pass to model methods
     * @return \CodeIgniter\HTTP\Response
     */
    public function handle(
        $model,
        array $columns,
        string $dataMethod,
        string $countMethod,
        ?callable $dataFormatter = null,
        array $additionalParams = []
    ): ResponseInterface {
        try {
            $params = $this->getRequestParameters($columns);
            
            // Get data from model with additional parameters
            $dataArgs = [
                $params['start'],
                $params['length'],
                $params['search'],
                $params['orderBy'],
                $params['orderDir']
            ];
            
            // Add additional parameters to the method call
            $dataArgs = array_merge($dataArgs, $additionalParams);
            
            $data = call_user_func_array([$model, $dataMethod], $dataArgs);

            // Format data if formatter provided
            if ($dataFormatter) {
                $data = array_map($dataFormatter, $data);
            }

            // For count method, we typically need fewer parameters
            $countArgs = !empty($additionalParams) ? $additionalParams : [$params['search']];
            $totalRecords = call_user_func_array([$model, $countMethod], $countArgs);

            return $this->buildResponse(
                $params['draw'],
                $totalRecords,
                $totalRecords,
                $data
            );

        } catch (InvalidArgumentException $e) {
            return $this->buildErrorResponse($e->getMessage());
        }
    }

    /**
     * Extract and validate DataTables request parameters
     */
    protected function getRequestParameters(array $columns): array
    {
        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start');
        $length = $this->request->getPost('length');
        $search = $this->request->getPost('search')['value'] ?? '';
        $orderColumn = $this->request->getPost('order')[0]['column'] ?? 0;
        $orderDir = strtoupper($this->request->getPost('order')[0]['dir'] ?? 'ASC');

        $this->validateParameters($start, $length, $orderColumn, $columns);

        return [
            'draw' => $draw,
            'start' => (int)$start,
            'length' => (int)$length,
            'search' => htmlspecialchars($search, ENT_QUOTES, 'UTF-8'),
            'orderBy' => $columns[$orderColumn] ?? $columns[0],
            'orderDir' => $orderDir === 'ASC' ? 'ASC' : 'DESC'
        ];
    }

    /**
     * Validate DataTables parameters
     * 
     * @throws InvalidArgumentException
     */
    protected function validateParameters($start, $length, $orderColumn, array $allowedColumns): void
    {
        if (!is_numeric($start) || !is_numeric($length)) {
            throw new InvalidArgumentException('Invalid pagination parameters');
        }

        if (!is_numeric($orderColumn) || $orderColumn < 0 || $orderColumn >= count($allowedColumns)) {
            throw new InvalidArgumentException('Invalid order column');
        }
    }

    /**
     * Build successful DataTable response
     */
    protected function buildResponse(
        $draw,
        int $recordsTotal,
        int $recordsFiltered,
        array $data
    ): ResponseInterface {
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Build error response
     */
    protected function buildErrorResponse(string $message): ResponseInterface
    {
        return $this->response->setJSON(['error' => $message])
                             ->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }
}