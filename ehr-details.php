<?php
session_start();
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    header("Location: login.php");
    exit;
}

include 'db_connection.php';

$doctor_id = $_SESSION['doctor_id'];

$ehr_id = isset($_GET['ehr_id']) ? filter_var($_GET['ehr_id'], FILTER_VALIDATE_INT) : 0;
if ($ehr_id <= 0) {
    echo "Invalid EHR ID.";
    exit;
}

function toDate($d) {
    if (empty($d)) return NULL;
    $parts = explode('-', $d);
    if (count($parts) === 3) {
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    return NULL;
}

function isValidDate($date, $format = 'd-m-Y') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function decodeText($text) {
    return htmlspecialchars(stripslashes($text), ENT_QUOTES);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $patientName = $conn->real_escape_string($_POST['patientName']);
    $dobInput = $_POST['dob'];
    $dobFormatted = isValidDate($dobInput) ? toDate($dobInput) : NULL;
    $gender = $conn->real_escape_string($_POST['gender']);
    $country = $conn->real_escape_string($_POST['country']);
    $appointmentDateInput = $_POST['appointmentDate'];
    $appointmentDateFormatted = isValidDate($appointmentDateInput) ? toDate($appointmentDateInput) : NULL;
    $appointmentNotes = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['appointmentNotes']));
    $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $otherPhone = filter_var($_POST['otherPhone'], FILTER_SANITIZE_NUMBER_INT);
    $insuranceNumber = $conn->real_escape_string($_POST['insuranceNumber']);
    $address = $conn->real_escape_string($_POST['address']);
    $postalCode = $conn->real_escape_string($_POST['postalCode']);
    $billingAddress = $conn->real_escape_string($_POST['billingAddress']);
    $amountToBePaid = floatval($_POST['amountToBePaid']);
    $symptoms = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['symptoms']));
    $maritalStatus = $conn->real_escape_string($_POST['maritalStatus']);
    $diagnosis = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['diagnosis']));
    $familyHistory = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['familyHistory']));
    $scanTests = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['scanTests']));
    $medications = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['medications']));
    $labTests = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['labTests']));
    $doctorNotes = str_replace("\r\n", "\n", $conn->real_escape_string($_POST['doctorNotes']));

    $stmt = $conn->prepare("UPDATE patient_ehr SET 
        patient_name=?, dob=?, gender=?, country=?, appointment_date=?, appointment_notes=?, mobile=?, email=?, 
        other_phone=?, address=?, insurance_number=?, postal_code=?, billing_address=?, amount_to_be_paid=?, 
        symptoms=?, marital_status=?, diagnosis=?, family_history=?, scan_tests=?, medications=?, lab_tests=?, 
        doctor_notes=? WHERE ehr_id=? AND doctor_id=?");

    $stmt->bind_param(
        "sssssssssssssssssssssdii",
        $patientName, $dobFormatted, $gender, $country, $appointmentDateFormatted, $appointmentNotes,
        $mobile, $email, $otherPhone, $address, $insuranceNumber, $postalCode, $billingAddress, $amountToBePaid,
        $symptoms, $maritalStatus, $diagnosis, $familyHistory, $scanTests, $medications, $labTests, $doctorNotes,
        $ehr_id, $doctor_id
    );

    if ($stmt->execute()) {
        $successMessage = "All patient information updated successfully!";
    } else {
        error_log("Error updating patient record: " . $conn->error);
        $errorMessage = "An error occurred while updating the patient record. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $stmt = $conn->prepare("DELETE FROM patient_ehr WHERE ehr_id=? AND doctor_id=?");
    $stmt->bind_param("ii", $ehr_id, $doctor_id);
    if ($stmt->execute()) {
        header("Location: patients.php");
        exit;
    } else {
        error_log("Error deleting patient record: " . $conn->error);
        $errorMessage = "An error occurred while deleting the patient record.";
    }
}

$stmt = $conn->prepare("SELECT * FROM patient_ehr WHERE ehr_id=? AND doctor_id=?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
if (!$patient) {
    echo "No patient found or you do not have access to this patient.";
    exit;
}

$decodedSymptoms = decodeText($patient['symptoms']);
$decodedFamilyHistory = decodeText($patient['family_history']);
$decodedScanTests = decodeText($patient['scan_tests']);
$decodedDiagnosis = decodeText($patient['diagnosis']);
$decodedMedications = decodeText($patient['medications']);
$decodedLabTests = decodeText($patient['lab_tests']);
$decodedDoctorNotes = decodeText($patient['doctor_notes']);

function displayDate($d) {
    if (empty($d) || $d === '0000-00-00') return '';
    $parts = explode('-', $d);
    return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
}

$displayDob = displayDate($patient['dob']);
$displayAppointmentDate = displayDate($patient['appointment_date']);

$stmt = $conn->prepare("SELECT image_id, image_path, image_data FROM ehr_images WHERE ehr_id=? AND doctor_id=?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);
$stmt->execute();
$images_result = $stmt->get_result();
$images = $images_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'upload_image') {
        $uploadedFiles = $_FILES['images'];
        $success = true;
        $uploadedData = [];

        foreach ($uploadedFiles['tmp_name'] as $key => $tmpName) {
            $fileType = mime_content_type($tmpName);
            if (!in_array($fileType, ['image/jpeg', 'image/png'])) {
                echo json_encode(['success' => false, 'error' => 'Invalid file type.']);
                exit;
            }
            if ($uploadedFiles['size'][$key] > 2 * 1024 * 1024) {
                echo json_encode(['success' => false, 'error' => 'File size exceeds 2MB limit.']);
                exit;
            }

            $fileName = basename($uploadedFiles['name'][$key]);
            $fileData = @file_get_contents($tmpName);
            if ($fileData === false) {
                $success = false;
                break;
            }

            $stmt = $conn->prepare("
                INSERT INTO ehr_images (ehr_id, doctor_id, image_path, image_data)
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisb", $ehr_id, $doctor_id, $fileName, $fileData);

            if (!$stmt->execute()) {
                $success = false;
                break;
            }

            $uploadedData[] = [
                'imageId' => $stmt->insert_id,
                'base64' => base64_encode($fileData),
                'fileName' => $fileName
            ];
        }

        echo json_encode(['success' => $success, 'uploadedData' => $uploadedData]);
        exit;
    }

    if ($_POST['action'] === 'delete_image') {
        $imageId = (int)$_POST['image_id'];
        $stmt = $conn->prepare("DELETE FROM ehr_images WHERE image_id=? AND doctor_id=?");
        $stmt->bind_param("ii", $imageId, $doctor_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient Details - MedConnectPro</title>
    <link rel="icon" href="img/logo.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="css/ehr-details.css" />
    <link rel="stylesheet" href="css/styles.css" />
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="doctor-dashboard.php"><img src="img/logo_main.png" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav" id="navbarLinks">
                    <li class="nav-item"><a class="nav-link" href="doctor-dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="patients.php">Your Patients</a></li>
                    <li class="nav-item"><a class="nav-link" href="ehr.php">New Patient</a></li>
                </ul>
                <a href="index.php" id="loginLogoutButton" class="btn btn-danger ms-auto">Logout</a>
            </div>
        </div>
    </nav>

    <form id="patientInfoForm" method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="" id="formAction" />
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 patient-sidebar">
                    <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="editPatientName" class="form-label fw-bold"><strong>Name:</strong></label>
                        <input type="text" class="form-control" id="editPatientName" name="patientName"
                            value="<?php echo htmlspecialchars($patient['patient_name'], ENT_QUOTES); ?>" />
                    </div>
                    <div class="mb-3">
                        <label for="editAge" class="form-label fw-bold"><strong>Date of Birth:</strong></label>
                        <input type="text" class="form-control mb-2 datepicker" id="editAge" name="dob"
                            value="<?php echo htmlspecialchars($displayDob); ?>" placeholder="dd-mm-yyyy" />
                    </div>
                    <div class="mb-3">
                        <label for="editGender" class="form-label fw-bold"><strong>Gender:</strong></label>
                        <select class="form-select" id="editGender" name="gender">
                            <option value="Male" <?php if($patient['gender'] === 'Male') echo 'selected'; ?>>Male
                            </option>
                            <option value="Female" <?php if($patient['gender'] === 'Female') echo 'selected'; ?>>
                                Female
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editCountry" class="form-label fw-bold"><strong>Country of
                                Origin:</strong></label>
                        <select class="form-select" id="editCountry" name="country">
                            <option value="" disabled>Select Country</option>
                            <option value="" disabled selected>Select Country</option>
                            <option value="Afghanistan">Afghanistan</option>
                            <option value="Albania">Albania</option>
                            <option value="Algeria">Algeria</option>
                            <option value="Andorra">Andorra</option>
                            <option value="Angola">Angola</option>
                            <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                            <option value="Argentina">Argentina</option>
                            <option value="Armenia">Armenia</option>
                            <option value="Australia">Australia</option>
                            <option value="Austria">Austria</option>
                            <option value="Azerbaijan">Azerbaijan</option>
                            <option value="Bahamas">Bahamas</option>
                            <option value="Bahrain">Bahrain</option>
                            <option value="Bangladesh">Bangladesh</option>
                            <option value="Barbados">Barbados</option>
                            <option value="Belarus">Belarus</option>
                            <option value="Belgium">Belgium</option>
                            <option value="Belize">Belize</option>
                            <option value="Benin">Benin</option>
                            <option value="Bhutan">Bhutan</option>
                            <option value="Bolivia">Bolivia</option>
                            <option value="Bosnia and Herzegovina">
                                Bosnia and Herzegovina
                            </option>
                            <option value="Botswana">Botswana</option>
                            <option value="Brazil">Brazil</option>
                            <option value="Brunei">Brunei</option>
                            <option value="Bulgaria">Bulgaria</option>
                            <option value="Burkina Faso">Burkina Faso</option>
                            <option value="Burundi">Burundi</option>
                            <option value="Cabo Verde">Cabo Verde</option>
                            <option value="Cambodia">Cambodia</option>
                            <option value="Cameroon">Cameroon</option>
                            <option value="Canada">Canada</option>
                            <option value="Central African Republic">
                                Central African Republic
                            </option>
                            <option value="Chad">Chad</option>
                            <option value="Chile">Chile</option>
                            <option value="China">China</option>
                            <option value="Colombia">Colombia</option>
                            <option value="Comoros">Comoros</option>
                            <option value="Congo (Congo-Brazzaville)">
                                Congo (Congo-Brazzaville)
                            </option>
                            <option value="Costa Rica">Costa Rica</option>
                            <option value="Croatia">Croatia</option>
                            <option value="Cuba">Cuba</option>
                            <option value="Cyprus">Cyprus</option>
                            <option value="Czechia (Czech Republic)">
                                Czechia (Czech Republic)
                            </option>
                            <option value="Democratic Republic of the Congo">
                                Democratic Republic of the Congo
                            </option>
                            <option value="Denmark">Denmark</option>
                            <option value="Djibouti">Djibouti</option>
                            <option value="Dominica">Dominica</option>
                            <option value="Dominican Republic">Dominican Republic</option>
                            <option value="Ecuador">Ecuador</option>
                            <option value="Egypt">Egypt</option>
                            <option value="El Salvador">El Salvador</option>
                            <option value="Equatorial Guinea">Equatorial Guinea</option>
                            <option value="Eritrea">Eritrea</option>
                            <option value="Estonia">Estonia</option>
                            <option value="Eswatini">Eswatini</option>
                            <option value="Ethiopia">Ethiopia</option>
                            <option value="Fiji">Fiji</option>
                            <option value="Finland">Finland</option>
                            <option value="France">France</option>
                            <option value="Gabon">Gabon</option>
                            <option value="Gambia">Gambia</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Germany">Germany</option>
                            <option value="Ghana">Ghana</option>
                            <option value="Greece">Greece</option>
                            <option value="Grenada">Grenada</option>
                            <option value="Guatemala">Guatemala</option>
                            <option value="Guinea">Guinea</option>
                            <option value="Guinea-Bissau">Guinea-Bissau</option>
                            <option value="Guyana">Guyana</option>
                            <option value="Haiti">Haiti</option>
                            <option value="Honduras">Honduras</option>
                            <option value="Hungary">Hungary</option>
                            <option value="Iceland">Iceland</option>
                            <option value="India">India</option>
                            <option value="Indonesia">Indonesia</option>
                            <option value="Iran">Iran</option>
                            <option value="Iraq">Iraq</option>
                            <option value="Ireland">Ireland</option>
                            <option value="Israel">Israel</option>
                            <option value="Italy">Italy</option>
                            <option value="Jamaica">Jamaica</option>
                            <option value="Japan">Japan</option>
                            <option value="Jordan">Jordan</option>
                            <option value="Kazakhstan">Kazakhstan</option>
                            <option value="Kenya">Kenya</option>
                            <option value="Kiribati">Kiribati</option>
                            <option value="Kuwait">Kuwait</option>
                            <option value="Kyrgyzstan">Kyrgyzstan</option>
                            <option value="Laos">Laos</option>
                            <option value="Latvia">Latvia</option>
                            <option value="Lebanon">Lebanon</option>
                            <option value="Lesotho">Lesotho</option>
                            <option value="Liberia">Liberia</option>
                            <option value="Libya">Libya</option>
                            <option value="Liechtenstein">Liechtenstein</option>
                            <option value="Lithuania">Lithuania</option>
                            <option value="Luxembourg">Luxembourg</option>
                            <option value="Madagascar">Madagascar</option>
                            <option value="Malawi">Malawi</option>
                            <option value="Malaysia">Malaysia</option>
                            <option value="Maldives">Maldives</option>
                            <option value="Mali">Mali</option>
                            <option value="Malta">Malta</option>
                            <option value="Marshall Islands">Marshall Islands</option>
                            <option value="Mauritania">Mauritania</option>
                            <option value="Mauritius">Mauritius</option>
                            <option value="Mexico">Mexico</option>
                            <option value="Micronesia">Micronesia</option>
                            <option value="Moldova">Moldova</option>
                            <option value="Monaco">Monaco</option>
                            <option value="Mongolia">Mongolia</option>
                            <option value="Montenegro">Montenegro</option>
                            <option value="Morocco">Morocco</option>
                            <option value="Mozambique">Mozambique</option>
                            <option value="Myanmar">Myanmar</option>
                            <option value="Namibia">Namibia</option>
                            <option value="Nauru">Nauru</option>
                            <option value="Nepal">Nepal</option>
                            <option value="Netherlands">Netherlands</option>
                            <option value="New Zealand">New Zealand</option>
                            <option value="Nicaragua">Nicaragua</option>
                            <option value="Niger">Niger</option>
                            <option value="Nigeria">Nigeria</option>
                            <option value="North Korea">North Korea</option>
                            <option value="North Macedonia">North Macedonia</option>
                            <option value="Norway">Norway</option>
                            <option value="Oman">Oman</option>
                            <option value="Pakistan">Pakistan</option>
                            <option value="Palau">Palau</option>
                            <option value="Palestine State">Palestine State</option>
                            <option value="Panama">Panama</option>
                            <option value="Papua New Guinea">Papua New Guinea</option>
                            <option value="Paraguay">Paraguay</option>
                            <option value="Peru">Peru</option>
                            <option value="Philippines">Philippines</option>
                            <option value="Poland">Poland</option>
                            <option value="Portugal">Portugal</option>
                            <option value="Qatar">Qatar</option>
                            <option value="Romania">Romania</option>
                            <option value="Russia">Russia</option>
                            <option value="Rwanda">Rwanda</option>
                            <option value="Saint Kitts and Nevis">
                                Saint Kitts and Nevis
                            </option>
                            <option value="Saint Lucia">Saint Lucia</option>
                            <option value="Saint Vincent and the Grenadines">
                                Saint Vincent and the Grenadines
                            </option>
                            <option value="Samoa">Samoa</option>
                            <option value="San Marino">San Marino</option>
                            <option value="Sao Tome and Principe">
                                Sao Tome and Principe
                            </option>
                            <option value="Saudi Arabia">Saudi Arabia</option>
                            <option value="Senegal">Senegal</option>
                            <option value="Serbia">Serbia</option>
                            <option value="Seychelles">Seychelles</option>
                            <option value="Sierra Leone">Sierra Leone</option>
                            <option value="Singapore">Singapore</option>
                            <option value="Slovakia">Slovakia</option>
                            <option value="Slovenia">Slovenia</option>
                            <option value="Solomon Islands">Solomon Islands</option>
                            <option value="Somalia">Somalia</option>
                            <option value="South Africa">South Africa</option>
                            <option value="South Korea">South Korea</option>
                            <option value="South Sudan">South Sudan</option>
                            <option value="Spain">Spain</option>
                            <option value="Sri Lanka">Sri Lanka</option>
                            <option value="Sudan">Sudan</option>
                            <option value="Suriname">Suriname</option>
                            <option value="Sweden">Sweden</option>
                            <option value="Switzerland">Switzerland</option>
                            <option value="Syria">Syria</option>
                            <option value="Tajikistan">Tajikistan</option>
                            <option value="Tanzania">Tanzania</option>
                            <option value="Thailand">Thailand</option>
                            <option value="Timor-Leste">Timor-Leste</option>
                            <option value="Togo">Togo</option>
                            <option value="Tonga">Tonga</option>
                            <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                            <option value="Tunisia">Tunisia</option>
                            <option value="Turkey">Turkey</option>
                            <option value="Turkmenistan">Turkmenistan</option>
                            <option value="Tuvalu">Tuvalu</option>
                            <option value="Uganda">Uganda</option>
                            <option value="Ukraine">Ukraine</option>
                            <option value="United Arab Emirates">
                                United Arab Emirates
                            </option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="United States">United States</option>
                            <option value="Uruguay">Uruguay</option>
                            <option value="Uzbekistan">Uzbekistan</option>
                            <option value="Vanuatu">Vanuatu</option>
                            <option value="Vatican City">Vatican City</option>
                            <option value="Venezuela">Venezuela</option>
                            <option value="Vietnam">Vietnam</option>
                            <option value="Yemen">Yemen</option>
                            <option value="Zambia">Zambia</option>
                            <option value="Zimbabwe">Zimbabwe</option>
                            <?php
                            $selectedCountry = $patient['country'];
                         
                            ?>
                            <option value="<?php echo htmlspecialchars($selectedCountry); ?>" selected>
                                <?php echo htmlspecialchars($selectedCountry); ?></option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editAppointments" class="form-label fw-bold"><strong>Appointments:</strong></label>
                        <input type="text" class="form-control mb-2 datepicker" id="appointmentDate"
                            name="appointmentDate" placeholder="dd-mm-yyyy"
                            value="<?php echo htmlspecialchars($displayAppointmentDate); ?>" />
                        <textarea class="form-control" id="editAppointments" name="appointmentNotes" rows="3"
                            placeholder="Enter additional appointment notes..."><?php echo htmlspecialchars($patient['appointment_notes']); ?></textarea>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" class="btn btn-gradient-purple w-100 me-2" id="savePatientInfo">Save
                            Changes</button>
                        <button type="button" class="btn btn-danger w-100" id="deletePatientInfo">Delete</button>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="ehr-section mt-2">
                        <h5 class="form-label fw-bold">PATIENT'S INFORMATION:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="mobile" class="mt-2 form-label fw-bold">Mobile:</label>
                                <textarea id="mobile" class="form-control"
                                    name="mobile"><?php echo htmlspecialchars($patient['mobile']); ?></textarea>

                                <label for="otherPhone" class="mt-3 form-label fw-bold">Other Phone:</label>
                                <textarea id="otherPhone" class="form-control"
                                    name="otherPhone"><?php echo htmlspecialchars($patient['other_phone']); ?></textarea>

                                <label for="address" class="mt-3 form-label fw-bold">Address:</label>
                                <textarea id="address" class="form-control"
                                    name="address"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="mt-2 form-label fw-bold">Email:</label>
                                <textarea id="email" class="form-control"
                                    name="email"><?php echo htmlspecialchars($patient['email']); ?></textarea>

                                <label for="insuranceNumber" class="mt-3 form-label fw-bold">Insurance
                                    Number:</label>
                                <textarea id="insuranceNumber" class="form-control"
                                    name="insuranceNumber"><?php echo htmlspecialchars($patient['insurance_number']); ?></textarea>

                                <label for="postalCode" class="mt-3 form-label fw-bold">Postal Code:</label>
                                <textarea id="postalCode" class="form-control"
                                    name="postalCode"><?php echo htmlspecialchars($patient['postal_code']); ?></textarea>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h6 class="mt-3 form-label fw-bold">PATIENT'S INVOICE:</h6>
                            <label for="billingAddress" class="mt-2 form-label fw-bold">Billing Address:</label>
                            <textarea id="billingAddress" class="form-control"
                                name="billingAddress"><?php echo htmlspecialchars($patient['billing_address']); ?></textarea>

                            <label for="amountToBePaid" class="mt-3 form-label fw-bold">Amount to be Paid:</label>
                            <textarea id="amountToBePaid" class="form-control"
                                name="amountToBePaid"><?php echo htmlspecialchars($patient['amount_to_be_paid']); ?></textarea>
                        </div>
                    </div>

                    <div class="ehr-section">
                        <h5 class="form-label fw-bold">MEDICAL INFORMATION:</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="symptoms" class="mt-2 form-label fw-bold">Symptoms:</label>
                                <textarea id="symptoms" class="form-control expandable-textarea"
                                    name="symptoms"><?php echo htmlspecialchars($decodedSymptoms); ?></textarea>

                                <label for="maritalStatus" class="form-label fw-bold mt-3">Marital Status:</label>
                                <select id="maritalStatus" class="form-select" name="maritalStatus">
                                    <option value="" disabled>Select Status</option>
                                    <option value="Single"
                                        <?php if($patient['marital_status'] === 'Single') echo 'selected'; ?>>
                                        Single
                                    </option>
                                    <option value="Married"
                                        <?php if($patient['marital_status'] === 'Married') echo 'selected'; ?>>
                                        Married
                                    </option>
                                </select>

                                <label for="diagnosis" class="form-label fw-bold mt-3">Diagnosis:</label>
                                <textarea id="diagnosis" class="form-control expandable-textarea"
                                    name="diagnosis"><?php echo htmlspecialchars($decodedDiagnosis); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="familyHistory" class="mt-2 form-label fw-bold">Family History:</label>
                                <textarea id="familyHistory" class="form-control expandable-textarea"
                                    name="familyHistory"><?php echo htmlspecialchars($decodedFamilyHistory); ?></textarea>

                                <label for="scanTests" class="form-label fw-bold mt-3">Scan Tests:</label>
                                <textarea id="scanTests" class="form-control expandable-textarea"
                                    name="scanTests"><?php echo htmlspecialchars($decodedScanTests); ?></textarea>

                                <label for="medications" class="form-label fw-bold mt-3">Medications:</label>
                                <textarea id="medications" class="form-control expandable-textarea"
                                    name="medications"><?php echo htmlspecialchars($decodedMedications); ?></textarea>
                            </div>
                        </div>

                        <label for="labTests" class="form-label fw-bold mt-3">Lab Tests:</label>
                        <textarea id="labTests" class="form-control expandable-textarea"
                            name="labTests"><?php echo htmlspecialchars($decodedLabTests); ?></textarea>

                        <label for="doctorNotes" class="form-label fw-bold mt-3">Doctor's Notes:</label>
                        <textarea class="form-control expandable-textarea" id="doctorNotes" name="doctorNotes"
                            rows="5"><?php echo htmlspecialchars($decodedDoctorNotes); ?></textarea>
                    </div>

                    <div class="ehr-section text-center" id="imagingSection">
                        <h5 class="mt-2 mb-3">Imaging & Diagrams</h5>
                        <div id="imageContainer" class="d-flex flex-wrap justify-content-center gap-3">
                            <?php if (!empty($images)): ?>
                            <?php foreach ($images as $image): ?>
                            <?php
                                    // Convert the BLOB to base64 so we can display it directly in the <img>
                                    $encodedImage = base64_encode($image['image_data']);
                                ?>
                            <div class="position-relative">
                                <img src="data:image/jpeg;base64,<?php echo $encodedImage; ?>"
                                    alt="<?php echo htmlspecialchars($image['image_path']); ?>" class="img-thumbnail"
                                    style="width: 500px;">
                                <button type="button" class="btn btn-danger btn-sm position-absolute"
                                    style="top: 5px; right: 5px;"
                                    onclick="deleteImage(<?php echo $image['image_id']; ?>)">
                                    ×
                                </button>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <p>No images uploaded yet.</p>
                            <?php endif; ?>
                        </div>
                        <div class="mt-3 mb-2">
                            <input type="file" id="uploadImageInput" name="images[]" class="d-none" accept="image/*"
                                multiple />
                            <button type="button" class="btn btn-gradient-purple"
                                onclick="document.getElementById('uploadImageInput').click()">
                                Upload Images
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>


    <footer class="footbar text-white text-center py-3">
        <p>&copy; 2024 MedConnectPro. All rights reserved.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js">
    </script>
    <script src="js/script.js"></script>
    <script src="js/ehr-details.js"></script>
</body>

</html>