<?php

class Admins extends Controller
{

  private $userModel;
  private $vehicleModel;
  private $bankModel;
  private $salaryModel;
  private $outsalaryModel;
  private $adminModel;
  private $workTripModel;
  private $postModel;




  public function __construct()
  {

    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }

    if (!isLoggedIn()) {
      $this->view('pages/index');
    }
    $this->userModel = $this->model('User');
    $this->postModel = $this->model('Post');
    $this->vehicleModel = $this->model('Vehicle');
    $this->workTripModel = $this->model('WorkTrip');
    // $this->bankModel = $this->model('BankModel');
    // $this->salaryModel = $this->model('Salary');
    // $this->outsalaryModel = $this->model('OutSalaryModel');
    // $this->adminModel = $this->model('Admin');





  }
  public function home()
  {
    $posts = $this->postModel->getPosts();
    $date = $this->postModel->getLastUpdatedDate();

    error_log("Last Updated Date: " . $date);

    if (!isLoggedIn()) {
      $this->view('pages/index');
    } else {
      $data = [
        'posts' => $posts,
        'lastdate' => $date
      ];
      $this->view('pages/admin/home_admin', $data);
    }
  }
  public function addVehicles()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Check if user is logged in (You may adjust this part based on your authentication system)
      if (!isset($_SESSION['user_id'])) {
        redirect('login');
      }

      // Get user ID from session
      $user_id = $_SESSION['user_id'];

      // Prepare data from the form
      $data = [
        'registration_number' => trim($_POST['reg_no']),
        'vehicle_number' => trim($_POST['v_no']),
        'vehicle_type' => trim($_POST['name']),
        'vehicle_model' => trim($_POST['model']),
        'registration_year' => trim($_POST['r_year']),
        'vin_number' => trim($_POST['vin']),
        'manufacture_year' => trim($_POST['year']),
        'insurance_company' => trim($_POST['insu_pro']),
        'insurance_number' => trim($_POST['insu_pn']),
        'passenger_capacity' => trim($_POST['passenger_capacity'])

      ];

      if ($this->vehicleModel->addVehicle($data)) {
        // Handle file uploads separately
        $front_photo = $this->uploadFile('image');
        $side_photo_1 = $this->uploadFile('images1');
        $side_photo_2 = $this->uploadFile('images2');

        // Update the database with file paths
        $this->vehicleModel->updatePhotos($front_photo, $side_photo_1, $side_photo_2);

        redirect('admins/addVehicles');
    } else {
        die('Something went wrong.'); // Handle error if insertion fails
    }
    } else {
      // If request method is not POST, load the view with empty form fields
      $data = [
        'registration_number' => '',
        'vehicle_number' => '',
        'vehicle_type' => '',
        'vehicle_model' => '',
        'registration_year' => '',
        'vin_number' => '',
        'manufacture_year' => '',
        'insurance_company' => '',
        'insurance_number' => '',
        'passenger_capacity' => ''
      ];
      $this->view('pages/admin/addVehicles', $data); // Load the view with form fields
    }
  }

  private function uploadFile($fileInputName)
{
    $uploadDirectory = 'uploads/';

    $fileName = basename($_FILES[$fileInputName]['name']);
    $targetFile = $uploadDirectory . $fileName;
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

    // Check if file already exists
    if (file_exists($targetFile)) {
        return null; // Return null if file already exists
    }

    // Check file size
    if ($_FILES[$fileInputName]['size'] > 500000) {
        return null; // Return null if file size exceeds limit
    }

    // Allow certain file formats
    if ($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif") {
        return null; // Return null if file format is not allowed
    }
    // Upload file
    if (move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFile)) {
        return $fileName; // Return the uploaded file name
    } else {
        return null; // Return null if file upload fails
    }
}


  // Function to handle form submission and insert salary details
  // function handleFormSubmission()
  // {
  //   // Check if form is submitted
  //   if ($_SERVER["REQUEST_METHOD"] == "POST") {
  //     $driver_id = $_POST['driver_id'];
  //     $number_of_trips = $_POST['number_of_trips'];
  //     $basic_payment = isset($_POST['edit_basic_payment']) ? $_POST['basic_payment'] : 2000;
  //     $total_amount = $basic_payment * $number_of_trips;
  //     $current_month = date("F"); // Month (Full name)
  //     $current_year = date("Y"); // Year (4-digit)

  //     $outSalaryModel = new OutSalaryModel();
  //     $data = [
  //       'driver_id' => $driver_id,
  //       'number_of_trips' => $number_of_trips,
  //       'basic_payment' => $basic_payment,
  //       'total_amount' => $total_amount,
  //       'month' => $current_month,
  //       'year' => $current_year
  //     ];
  //     if ($outSalaryModel->insertSalary($data)) {
  //       echo "Data inserted successfully!";
  //     } else {
  //       echo "Error inserting salary details.";
  //     }

  //     $this->view('pages/admin/out_salary', $data);
  //   } else {
  //     // If request method is not POST, load the salary calculation view
  //     $this->view('pages/admin/out_salary');
  //   }
  // }
  function redirectToTimetable()
  {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['driver_id'])) {
      $driver_id = $_GET['driver_id'];
      header("Location: view_schedule.php?driver_id=$driver_id");
      exit();
    }
    $this->view('pages/admin/view_schedule', );
  }


  public function viewvehicle()
  {
    // Retrieve all vehicle details
    $vehicles = $this->vehicleModel->getVehicleDetails();

    if (!isLoggedIn()) {
      $this->view('pages/index');
    } else {
      $data = [
        'vehicles' => $vehicles
      ];
    // Pass the data to the view
    $this->view('pages/admin/viewVehicles', $data);
    }
  }

  public function viewvehiclemore($reg_no)
  {
    // Retrieve all vehicle details
    $vehicles = $this->vehicleModel->get_vehicle_details($reg_no);

    if (!isLoggedIn()) {
      $this->view('pages/index');
    } else {
      $data = [
        'vehicles' => $vehicles
      ];
    // Pass the data to the view
    $this->view('pages/admin/viewVehicles_more', $data);
    }
  }

  

  //edit vehicle
public function editVehicle($reg_no)
{
    //view previous details
    $vehicles = $this->vehicleModel->get_vehicle_details($reg_no);
    $data = [
        'registration_number' => $reg_no,
        'vehicle_number' => $vehicles->Vehicle_Number,
        'vehicle_type' => $vehicles->Vehicle_Name,
        'vehicle_model' => $vehicles->model,
        'registration_year' => $vehicles->r_year,
        'vin_number' => $vehicles->vin,
        'manufacture_year' => $vehicles->Vehicle_Year,
        'insurance_company' => $vehicles->insu_pro,
        'insurance_number' => $vehicles->insu_pn,
        'passenger_capacity' => $vehicles->capacity
    ];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Prepare data from the form
        $data = [
            'registration_number' => $reg_no,
            'vehicle_number' => trim($_POST['vehicleNumber']),
            'vehicle_type' => trim($_POST['vehicleType']),
            'vehicle_model' => trim($_POST['vehicleModel']),
            'registration_year' => trim($_POST['registrationYear']),
            'vin_number' => trim($_POST['vin']),
            'manufacture_year' => trim($_POST['vehicleYear']),
            'insurance_company' => trim($_POST['insuranceCo']),
            'insurance_number' => trim($_POST['insuranceNo']),
            'passenger_capacity' => intval($_POST['capacity'])
        ];

        // Call the addVehicle method from the model to insert the data into the database
        if ($this->vehicleModel->editVehicle($data)) {
            redirect('admins/viewVehicle'); // Redirect after successful insertion
        } else {
            die('Something went wrong.'); // Handle error if insertion fails
        }
    } else {
        // If request method is not POST, load the view with previous details
        $this->view('pages/admin/edit_vehicle', $data); // Load the view with form fields
    }
}

public function deleteVehicle($reg_no)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Handle the delete using $reg_no
        if ($this->vehicleModel->deleteVehicle($reg_no)) {
            flash('post_message', 'Vehicle deleted');
            redirect('admins/viewvehicle');
        } else {
            die('Something went wrong');
        }
    } else {
        // Handle if the POST request is not properly set
        redirect('admins/viewvehicle');
    }
}



  // Method to handle displaying available vehicles
  public function availableVehicles()
  {

    $bookedDays = $this->vehicleModel->getBookedDays();

    include ('pages/admin/available_vehicles.php');
  }
  public function viewSchedule($day, $timeSlot)
  {
    include 'Model.php'; // Include the Model

    $timetable = $model->getTimetable($day, $timeSlot);

    if ($timetable !== null) {
      $this->view('pages/admin/schedule_view', );// Include the View
    } else {
      echo "No timetable data available.";
    }
  }







  public function asEmployee()
  {

    // Check for POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Process form

      // Sanitize POST data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      //init data
      $data = [
        'email' => $_SESSION['user_email'], // Use the logged-in user's email
        'password' => trim($_POST['password']),
        'password_err' => '',
      ];

      // Validate Password
      if (empty($data['password'])) {
        $data['password_err'] = 'Please enter password';
      }

      // Make sure errors are empty
      if (empty($data['password_err'])) {
        // Validated

        // Check and set logged in user
        $loggedInUser = $this->userModel->login($data['email'], $data['password']);
        if ($loggedInUser) {

          redirect('employees/home');
        } else {
          $data['password_err'] = 'Password incorrect';
          $this->view('pages/admin/as_employee', $data);
        }

      } else {
        // Load view with errors
        $this->view('pages/admin/as_employee', $data);
      }

    } else {
      // Init data
      $data = [
        'email' => $_SESSION['user_email'], // Use the logged-in user's email
        'password' => '',
        'password_err' => ''
      ];
      // Load view
      $this->view('pages/admin/as_employee', $data);
    }
  }
  public function viewhr()
  {
    $hrmanagers = $this->userModel->getHrmanager();
    $data = [
      'hrmanagers' => $hrmanagers
    ];
    $this->view('pages/admin/view_hr', $data);
  }
  public function deleteHR($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Handle the delete using $id
      // For example:
      if ($this->userModel->deleteEmployee($id)) {
        flash('post_message', 'HrManager deleted');
        redirect('admins/viewhr');
      } else {
        die('Something went wrong');
      }
    } else {
      // Handle if the POST request is not properly set
      redirect('admins/viewhr');
    }
  }
  public function updateHRStatus($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Handle the update using $id and $action
      // For example:
      $action = $_POST['action'];
      if ($action === 'approve') {
        $status = 'approved';
      } elseif ($action === 'reject') {
        $status = 'rejected';
      } elseif ($action === 'approve') {
        $status = 'approved';
      } else {
        // Handle invalid action
        die('Invalid action');
      }

      if ($this->userModel->updatestatus($id, $status)) {
        flash('post_message', 'HR Manager status updated');
        redirect('admins/viewhr');
      } else {
        die('Something went wrong');
      }
    } else {
      // Handle if the POST request is not properly set
      redirect('admins/viewhr');
    }
  }
  public function viewFullDayBooking()
  {


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {


      //sanitize
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = array(
        'b_date' => trim($_POST['b_date']),
        'location' => trim($_POST['location']),
        'no_pas' => trim($_POST['no_pas']),
        'driver_id' => trim($_POST['driver_id']),
        'vehicle_id' => trim($_POST['vehicle_id']),
        'b_date_err' => '',
        'location_err' => '',
        'no_pas_err' => '',
        'driver_id_err' => '',
        'vehicle_id_err' => ''
      )
      ;


      // $this->adminModel->addbooking($data);


      if ($this->adminModel->addbooking($data)) {
        echo ('booking Added Succesfully');

        redirect('admins/viewFullDayBooking');
      } else {
        die('Something went wrong');
      }


    } else {
      $data = [
        'title' => '',
        'body' => '',
        'title_err' => '',
        'body_err' => ''
      ];

      $this->view('pages/admin/full_day_booking', $data);
    }
  }



  public function showTimetable()
  {
    // Fetch all data from the fullday_timetable table sorted by date
    $timetableData = $this->adminModel->getbooking();

    // Pass the data to the view
    return $this->view('pages/admin/full_booking', ['timetableData' => $timetableData]);
  }

  public function viewPendingWorkTrips()
{
    $pendingWorkTrips = $this->workTripModel->getPendingWorkTripReservations(); // Fetch pending work trips from the model
    $data = [
        'pendingWorkTrips' => $pendingWorkTrips
    ];
    $this->view('pages/admin/viewPendingWorkTrips', $data); // Pass the data to the view
}

  public function approveWorkTrip($tripID)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the action (approve or reject)
        $action = $_POST['action'];

        // Update the status of the work trip based on the action
        if ($action === 'approve') {
            $status = 'approved';
        } elseif ($action === 'reject') {
            $status = 'rejected';
        } else {
            // Handle invalid action
            die('Invalid action');
        }

        // Update the status in the database
        if ($this->workTripModel->updateStatus($tripID, $status)) {
            // Redirect to the view pending work trips page
            redirect('admins/viewPendingWorkTrips');
        } else {
            die('Something went wrong');
        }
    } else {
        // Handle if the request method is not POST
        redirect('admins/viewPendingWorkTrips');
    }
}

}
































