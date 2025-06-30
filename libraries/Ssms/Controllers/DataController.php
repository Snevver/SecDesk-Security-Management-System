<?php

namespace Ssms\Controllers;

use Ssms\Logger;

class DataController
{
    public function __construct(private \PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTest($test_id)
    {
        try {

            Logger::write('info', 'Fetching test data for test ID: ' . $test_id . " of type " . gettype($test_id));

            // Prepare and execute the SQL statement to fetch test data
            $stmt = $this->pdo->prepare("SELECT * FROM tests WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the test data
            $testData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($testData) {
                Logger::write('info', 'Fetched test data for test ID ' . $test_id . ': ' . json_encode($testData));
                return [
                    'status' => 200,
                    'data' => $testData
                ];
            } else {
                Logger::write('error', 'No test found for test ID ' . $test_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Test not found']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function getTarget($target_id)
    {
        try {
            Logger::write('info', 'Fetching target data for target ID: ' . $target_id);

            // Prepare and execute the SQL statement to fetch target data
            $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the target data
            $targetData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($targetData) {
                Logger::write('info', 'Fetched target data for target ID ' . $target_id . ': ' . json_encode($targetData));
                return [
                    'status' => 200,
                    'data' => $targetData
                ];
            } else {
                Logger::write('error', 'No target found for target ID ' . $target_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Target not found']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function getVulnerability($vulnerability_id) {
        try {
            Logger::write('info', 'Fetching vulnerability data for vulnerability ID: ' . $vulnerability_id);

            // Prepare and execute the SQL statement to fetch vulnerability data
            $stmt = $this->pdo->prepare("SELECT * FROM vulnerabilities WHERE id = :vulnerability_id");
            $stmt->bindParam(':vulnerability_id', $vulnerability_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the vulnerability data
            $vulnerabilityData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($vulnerabilityData) {
                Logger::write('info', 'Fetched vulnerability data for vulnerability ID ' . $vulnerability_id . ': ' . json_encode($vulnerabilityData));
                return [
                    'status' => 200,
                    'data' => $vulnerabilityData
                ];
            } else {
                Logger::write('error', 'No vulnerability found for vulnerability ID ' . $vulnerability_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Vulnerability not found']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function updateTest($test_id, $title, $description)
    {
        try {
            Logger::write('info', 'Updating test with ID: ' . $test_id);

            // Prepare and execute the SQL statement to update the test
            $stmt = $this->pdo->prepare("UPDATE tests SET test_name = :title, test_description = :description WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                Logger::write('info', 'Test updated successfully for test ID ' . $test_id);
                return [
                    'status' => 200,
                    'data' => ['success' => true]
                ];
            } else {
                Logger::write('error', 'No changes made for test ID ' . $test_id);
                return [
                    'status' => 304,
                    'data' => ['success' => false, 'message' => 'No changes made']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    public function updateTarget($target_id, $title, $description)
    {
        try {
            Logger::write('info', 'Updating target with ID: ' . $target_id);

            // Prepare and execute the SQL statement to update the target
            $stmt = $this->pdo->prepare("UPDATE targets SET target_name = :title, target_description = :description WHERE id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                Logger::write('info', 'Target updated successfully for target ID ' . $target_id);
                return [
                    'status' => 200,
                    'data' => ['success' => true]
                ];
            } else {
                Logger::write('error', 'No changes made for target ID ' . $target_id);
                return [
                    'status' => 304,
                    'data' => ['success' => false, 'message' => 'No changes made']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }

    //-----------------------------------------------------
    // Update Vulnerability
    //-----------------------------------------------------
    public function updateVulnerability($vulnerability_id, $affected_entity, $identifier, $risk_statement, $affected_component, $residual_risk, $classification, $identified_controls, $cvss_score, $likelihood, $cvssv3_code, $location, $vulnerabilities_description, $reproduction_steps, $impact, $remediation_difficulty, $recommendations, $recommended_reading, $response, $solved)
    {
        try {
            Logger::write('info', 'Updating vulnerability with ID: ' . $vulnerability_id);

            // Prepare and execute the SQL statement to update the vulnerability
            $stmt = $this->pdo->prepare("UPDATE vulnerabilities SET affected_entity = :affected_entity, identifier = :identifier, risk_statement = :risk_statement, affected_component = :affected_component, residual_risk = :residual_risk, classification = :classification, identified_controls = :identified_controls, cvss_score = :cvss_score, likelihood = :likelihood, cvssv3_code = :cvssv3_code, location = :location, vulnerabilities_description = :vulnerabilities_description, reproduction_steps = :reproduction_steps, impact = :impact, remediation_difficulty = :remediation_difficulty, recommendations = :recommendations, recommended_reading = :recommended_reading, response = :response, solved = :solved WHERE id = :vulnerability_id");
            
            // Bind parameters
            $stmt->bindParam(':vulnerability_id', $vulnerability_id);
            $stmt->bindParam(':affected_entity', $affected_entity);
            $stmt->bindParam(':identifier', $identifier);
            $stmt->bindParam(':risk_statement', $risk_statement);
            $stmt->bindParam(':affected_component', $affected_component);
            $stmt->bindParam(':residual_risk', $residual_risk);
            $stmt->bindParam(':classification', $classification);
            $stmt->bindParam(':identified_controls', $identified_controls);
            $stmt->bindParam(':cvss_score', $cvss_score);
            $stmt->bindParam(':likelihood', $likelihood);
            $stmt->bindParam(':cvssv3_code', $cvssv3_code);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':vulnerabilities_description', $vulnerabilities_description);
            $stmt->bindParam(':reproduction_steps', $reproduction_steps);
            $stmt->bindParam(':impact', $impact);
            $stmt->bindParam(':remediation_difficulty', $remediation_difficulty);
            $stmt->bindParam(':recommendations', $recommendations);
            $stmt->bindParam(':recommended_reading', $recommended_reading);
            $stmt->bindParam(':response', $response);
            $stmt->bindParam(':solved', $solved, \PDO::PARAM_BOOL);
            $stmt->execute();

            Logger::write('info', 'Updated vulnerability with ID: ' . $vulnerability_id);
            return [
                'status' => 200,
                'data' => ['message' => 'Vulnerability updated successfully']
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }

    //-----------------------------------------------------
    // Fetch all target data from the database
    //-----------------------------------------------------
    public function getTargets($test_id = null)
    { 
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM targets WHERE test_id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->execute();
            $targets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
             
            Logger::write('info', 'Fetched targets for test ID ' . $test_id . ': ' . json_encode($targets));

            return [
                'status' => 200,
                'data' => ['targets' => $targets]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }

    public function getCustomerEmail($customer_id)
    {
        try {
            Logger::write('info', 'Fetching customer email for customer ID: ' . $customer_id);

            // Prepare and execute the SQL statement to fetch customer email
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE id = :customer_id");
            $stmt->bindParam(':customer_id', $customer_id, \PDO::PARAM_INT);
            $stmt->execute();
            
            // Fetch the customer email
            $customerEmail = $stmt->fetchColumn();
            
            if ($customerEmail) {
                Logger::write('info', 'Fetched customer email for customer ID ' . $customer_id . ': ' . $customerEmail);
                return [
                    'status' => 200,
                    'data' => ['email' => $customerEmail]
                ];
            } else {
                Logger::write('error', 'No customer found for customer ID ' . $customer_id);
                return [
                    'status' => 404,
                    'data' => ['error' => 'Customer not found']
                ];
            }
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
            ];
        }
    }
}