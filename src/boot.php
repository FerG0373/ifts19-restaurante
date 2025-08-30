<?php
use App\Services\DatabaseService;

$dbService = new DatabaseService();
$db = $dbService->getAccesoDatos();