<?php

namespace Ssms\Controllers;

use Ssms\Logger;
use Ssms\InputValidator;

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
            // Validate test ID
            $idValidation = InputValidator::validateId($test_id, 'Test ID');
            if (!$idValidation['valid']) {
                return [
                    'status' => 400,
                    'data' => ['error' => $idValidation['error']]
                ];
            }
            $test_id = $idValidation['value'];
            
            // Validate and sanitize title
            $titleValidation = InputValidator::validateTestTitle($title);
            if (!$titleValidation['valid']) {
                return [
                    'status' => 400,
                    'data' => ['error' => $titleValidation['error']]
                ];
            }
            $sanitizedTitle = $titleValidation['value'];
            
            // Validate and sanitize description
            $descValidation = InputValidator::validateTestDescription($description);
            $sanitizedDescription = $descValidation['value'];
            
            Logger::write('info', 'Updating test with ID: ' . $test_id);

            // Prepare and execute the SQL statement to update the test
            $stmt = $this->pdo->prepare("UPDATE tests SET test_name = :title, test_description = :description WHERE id = :test_id");
            $stmt->bindParam(':test_id', $test_id, \PDO::PARAM_INT);
            $stmt->bindParam(':title', $sanitizedTitle, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $sanitizedDescription, \PDO::PARAM_STR);
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
            // Validate target ID
            $idValidation = InputValidator::validateId($target_id, 'Target ID');
            if (!$idValidation['valid']) {
                return [
                    'status' => 400,
                    'data' => ['error' => $idValidation['error']]
                ];
            }
            $target_id = $idValidation['value'];
            
            // Validate and sanitize title
            $titleValidation = InputValidator::validateTargetName($title);
            if (!$titleValidation['valid']) {
                return [
                    'status' => 400,
                    'data' => ['error' => $titleValidation['error']]
                ];
            }
            $sanitizedTitle = $titleValidation['value'];
            
            // Validate and sanitize description
            $descValidation = InputValidator::validateTargetDescription($description);
            $sanitizedDescription = $descValidation['value'];
            
            Logger::write('info', 'Updating target with ID: ' . $target_id);

            // Prepare and execute the SQL statement to update the target
            $stmt = $this->pdo->prepare("UPDATE targets SET target_name = :title, target_description = :description WHERE id = :target_id");
            $stmt->bindParam(':target_id', $target_id, \PDO::PARAM_INT);
            $stmt->bindParam(':title', $sanitizedTitle, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $sanitizedDescription, \PDO::PARAM_STR);
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
    public function updateVulnerability($vulnerability_id, $affected_entity, $identifier, $risk_statement, $affected_component, $residual_risk, $classification, $identified_controls, $cvss_score, $likelihood, $cvssv3_code, $location, $vulnerabilities_description, $reproduction_steps, $impact, $remediation_difficulty, $recommendations, $recommended_reading, $response)
    {
        try {
            // Validate vulnerability ID
            $idValidation = InputValidator::validateId($vulnerability_id, 'Vulnerability ID');
            if (!$idValidation['valid']) {
                return [
                    'status' => 400,
                    'data' => ['error' => $idValidation['error']]
                ];
            }
            $vulnerability_id = $idValidation['value'];
            
            // Validate and sanitize all vulnerability fields
            $sanitizedData = [
                'affected_entity' => InputValidator::validateVulnerabilityField($affected_entity, 'Affected Entity', 255)['value'],
                'identifier' => InputValidator::validateVulnerabilityField($identifier, 'Identifier', 255)['value'],
                'risk_statement' => InputValidator::validateVulnerabilityField($risk_statement, 'Risk Statement', 2000)['value'],
                'affected_component' => InputValidator::validateVulnerabilityField($affected_component, 'Affected Component', 500)['value'],
                'residual_risk' => InputValidator::validateVulnerabilityField($residual_risk, 'Residual Risk', 500)['value'],
                'classification' => InputValidator::validateVulnerabilityField($classification, 'Classification', 255)['value'],
                'identified_controls' => InputValidator::validateVulnerabilityField($identified_controls, 'Identified Controls', 2000)['value'],
                'cvss_score' => InputValidator::validateVulnerabilityField($cvss_score, 'CVSS Score', 50)['value'],
                'likelihood' => InputValidator::validateVulnerabilityField($likelihood, 'Likelihood', 255)['value'],
                'cvssv3_code' => InputValidator::validateVulnerabilityField($cvssv3_code, 'CVSS v3 Code', 255)['value'],
                'location' => InputValidator::validateVulnerabilityField($location, 'Location', 500)['value'],
                'vulnerabilities_description' => InputValidator::validateVulnerabilityField($vulnerabilities_description, 'Description', 5000)['value'],
                'reproduction_steps' => InputValidator::validateVulnerabilityField($reproduction_steps, 'Reproduction Steps', 5000)['value'],
                'impact' => InputValidator::validateVulnerabilityField($impact, 'Impact', 2000)['value'],
                'remediation_difficulty' => InputValidator::validateVulnerabilityField($remediation_difficulty, 'Remediation Difficulty', 500)['value'],
                'recommendations' => InputValidator::validateVulnerabilityField($recommendations, 'Recommendations', 5000)['value'],
                'recommended_reading' => InputValidator::validateVulnerabilityField($recommended_reading, 'Recommended Reading', 2000)['value'],
                'response' => InputValidator::validateVulnerabilityField($response, 'Response', 2000)['value']
            ];
            
            Logger::write('info', 'Updating vulnerability with ID: ' . $vulnerability_id);

            // Prepare and execute the SQL statement to update the vulnerability
            $stmt = $this->pdo->prepare("UPDATE vulnerabilities SET affected_entity = :affected_entity, identifier = :identifier, risk_statement = :risk_statement, affected_component = :affected_component, residual_risk = :residual_risk, classification = :classification, identified_controls = :identified_controls, cvss_score = :cvss_score, likelihood = :likelihood, cvssv3_code = :cvssv3_code, location = :location, vulnerabilities_description = :vulnerabilities_description, reproduction_steps = :reproduction_steps, impact = :impact, remediation_difficulty = :remediation_difficulty, recommendations = :recommendations, recommended_reading = :recommended_reading, response = :response WHERE id = :vulnerability_id");
            
            // Bind parameters with sanitized data
            $stmt->bindParam(':vulnerability_id', $vulnerability_id, \PDO::PARAM_INT);
            $stmt->bindParam(':affected_entity', $sanitizedData['affected_entity']);
            $stmt->bindParam(':identifier', $sanitizedData['identifier']);
            $stmt->bindParam(':risk_statement', $sanitizedData['risk_statement']);
            $stmt->bindParam(':affected_component', $sanitizedData['affected_component']);
            $stmt->bindParam(':residual_risk', $sanitizedData['residual_risk']);
            $stmt->bindParam(':classification', $sanitizedData['classification']);
            $stmt->bindParam(':identified_controls', $sanitizedData['identified_controls']);
            $stmt->bindParam(':cvss_score', $sanitizedData['cvss_score']);
            $stmt->bindParam(':likelihood', $sanitizedData['likelihood']);
            $stmt->bindParam(':cvssv3_code', $sanitizedData['cvssv3_code']);
            $stmt->bindParam(':location', $sanitizedData['location']);
            $stmt->bindParam(':vulnerabilities_description', $sanitizedData['vulnerabilities_description']);
            $stmt->bindParam(':reproduction_steps', $sanitizedData['reproduction_steps']);
            $stmt->bindParam(':impact', $sanitizedData['impact']);
            $stmt->bindParam(':remediation_difficulty', $sanitizedData['remediation_difficulty']);
            $stmt->bindParam(':recommendations', $sanitizedData['recommendations']);
            $stmt->bindParam(':recommended_reading', $sanitizedData['recommended_reading']);
            $stmt->bindParam(':response', $sanitizedData['response']);
            $stmt->execute();

            Logger::write('info', 'Updated vulnerability with ID: ' . $vulnerability_id);
            return [
                'status' => 200,
                'data' => ['success' => true]
            ];
        } catch (\PDOException $e) {
            Logger::write('error', 'Database error: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Database error']
            ];
        }
    }

    /**
     * Update the solved status of a vulnerability
     */
    public function updateVulnerabilitySolved()
    {
        try {
            $requestBody = file_get_contents('php://input');
            $data = json_decode($requestBody, true);

            if (!isset($data['vulnerability_id']) || !isset($data['solved'])) {
                return [
                    'status' => 400,
                    'data' => ['error' => 'Missing vulnerability_id or solved status']
                ];
            }

            $vulnerabilityId = InputValidator::validateInteger($data['vulnerability_id']);
            $solved = InputValidator::validateBoolean($data['solved']);

            if ($vulnerabilityId === null) {
                return [
                    'status' => 400,
                    'data' => ['error' => 'Invalid vulnerability ID']
                ];
            }

            Logger::write('info', "Updating vulnerability {$vulnerabilityId} solved status to: " . ($solved ? 'true' : 'false'));

            $stmt = $this->pdo->prepare("UPDATE vulnerabilities SET solved = :solved WHERE id = :vulnerability_id");
            $stmt->bindParam(':solved', $solved, \PDO::PARAM_BOOL);
            $stmt->bindParam(':vulnerability_id', $vulnerabilityId, \PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                Logger::write('info', "Successfully updated vulnerability {$vulnerabilityId} solved status");
                return [
                    'status' => 200,
                    'data' => ['success' => true, 'message' => 'Vulnerability solved status updated successfully']
                ];
            } else {
                Logger::write('error', "Failed to update vulnerability {$vulnerabilityId} solved status");
                return [
                    'status' => 500,
                    'data' => ['error' => 'Failed to update vulnerability solved status']
                ];
            }
        } catch (\Exception $e) {
            Logger::write('error', 'Error updating vulnerability solved status: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => ['error' => 'Internal server error']
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