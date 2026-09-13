<?php
    require_once("../../include/server/index.php");
    session_start();
    if (!isset($_SESSION['adminId'])) {
        header('Location: /admin/sign-in/');
        exit();
    }
/* check if click search button */
    if (isset($_GET['search'])) {
    /* filter data */
        $search = trim($_GET['search']);
        $secSearch = mysqli_real_escape_string($conn, $search);
        $sql = "SELECT
            `productId`,
            `productName`,
            `description`
        FROM
            `Product`
        WHERE
            `category` LIKE '%$secSearch%' AND
            `trash` = 0
        ORDER BY
            `productId` DESC,
            `productName` ASC,
            `quantityInStock` ASC,
            `category` ASC
        ";
    } else {
    /* defalut load data */
        $search = '';
        $sql = "SELECT
            `productId`,
            `productName`,
            `description`
        FROM
            `Product`
        WHERE
            `trash` = 0
        ORDER BY
            `productId` DESC,
            `productName` ASC,
            `quantityInStock` ASC,
            `category` ASC
        ";
    }
/* prepare the query */
    $stmt = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dilla's PC - All Saved Items</title>
    <link rel="icon" href="/image/logo.jpg">
    <link rel="stylesheet" href="/css/style.css">
    <style type="text/css">
        main .search-form {
            box-sizing: border-box;
        }
        main .search-form input[type="search"] {
            width: 100%;
            margin: auto;
            padding: 14px;
            border: none;
            outline: none;
            background-color: #ddd;
            color: #000000;
        }
        main .search-form input[type="search"]::placeholder {
            color: #808080;
        }
        main .result-header {
            width: 80%;
            margin: auto;
            margin-top: 100px;
            padding: 14px;
            border: 1px solid #fff;
            box-sizing: border-box;
            border-radius: 4px 4px 0 0;
            background-color: #909090;
            color: #000000;
        }
    /* search result styles */
        main .search-div {
            width: 80%;
            max-height:300px;
            margin: auto;
            overflow: auto;
        }
        main .search-div table {
            min-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 20px;
        }
        main .search-div table th, main .search-div table td {
            width: 200px;
            min-width: min-content;
        }
        main .search-div table th {
            padding: 14px;
            font-size: large;
            text-transform: uppercase;
            background-color: #909090;
            color: #000000;
        }
        main .search-div table td {
            padding: 15px;
            text-align: center;
            background-color: #ddd;
            color: #000000;
            border-bottom: 1px solid #808080;
        }
    /* information style */
        main .search-div .custom-note {
            margin: 4px auto;
            padding: 8px;
            background-color: #808080;
            color: #fff;
        }
    /* back btn style */
        main .bottom-div, main .top-div {
            width: 80%;
            margin: auto;
            overflow: auto;
        }
        main .bottom-div button {
            margin: auto;
            margin-top: 20px;
            margin-left: 8px;
            padding: 8px;
            cursor: pointer;
        }
        main .top-div button, main .top-div select {
            margin: 15px;
            padding: 8px;
            cursor: pointer;
        }
        main .top-div fieldset {
            margin-bottom: 10px;
        }
        main .top-div legend {
            margin: 10px;
            padding: 5px;
        }
    /* PDF paper style */
        .quote-outline-border, .quote-outline-border .quote-outline {
            width: 100%;
            box-sizing: border-box;
            padding: 25px;
        }
        .quote-outline-border {
            width: 210mm;
            min-height: 297mm;
            margin: auto;
            background-color: #fff;
            color: #000000;
            display: none;
        }
        .quote-outline-border .quote-outline {
            min-height: 297mm;
            border: 1px solid #000000;
        }
        .quote-outline-border .quote-outline .img-border {
            text-align: right;
        }
        .quote-outline-border .quote-outline .img-border img {
            width: 85px;
            display: inline-block;
        }
        .quote-outline-border .quote-outline p {
            color: #808080;
        }
        .quote-outline-border .quote-outline h3 {
            margin: 15px 0 5px 0;
        }
        .quote-outline-border .quote-outline table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .quote-outline-border .quote-outline .header-part-1, .quote-outline-border .quote-outline .header-part-2 {
            width: 50%;
            float: left;
            margin-bottom: 50px;
        }
        .quote-outline-border .quote-outline .header-part-1 p {
            color: #000000;
        }
        .quote-outline-border .quote-outline .header-part-2 table th {
            text-align: right;
        }
        .quote-outline-border .quote-outline .item-table-layout {
            width: 100%;
        }
        .quote-outline-border .quote-outline .item-table-layout table thead th {
            padding: 10px;
            text-align: right;
            background-color: #ddd;
        }
        .quote-outline-border .quote-outline .item-table-layout table td {
            padding: 10px;
            text-align: right;
        }
        .quote-outline-border .quote-outline .item-table-layout table #first-column {
            text-align: left;
        }
        .quote-outline-border .quote-outline .item-table-layout table tr {
            border: 1px solid #909090;
        }
        #total-table, #total-table * {
            border: none;
        }
        #total-table * {
            margin: 0;
            padding: 0;
        }
        #total-table #main-table-column table {
            border: 1px solid #909090;
            margin-top: 25px;
        }
        #total-table #main-table-column table th, #total-table #main-table-column table td {
            padding: 10px;
        }
        #total-table #main-table-column table th {
            text-align: left;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
    <header>
        <div class="main-nav-btn">
            <a href="/" id="home-nav" title="Home"><img src="/image/logo.jpg" alt=""></a>
            <a href="/product-category/laptop/"><img src="/image/laptop.png" alt=""> Laptop</a>
            <a href="/product-category/desktop/"><img src="/image/desktop.png" alt=""> Desktop</a>
            <a href="/product-category/monitor/"><img src="/image/monitor.jpg" alt=""> Monitor</a>
            <a href="/product-category/computer-accessories/"><img src="/image/accessories.png" alt=""> Accessories</a>
        </div>
    </header>
    <main>
        <form action="/search/" method="get" autocomplete="off" class="search-form">
            <input type="search" name="result" placeholder="Enter anything computer products related.. 🔍" maxlength="100" required autofocus>
        </form>
        <h3 class="result-header">All Saved Items 🔐</h3>
        <div class="top-div">
        <!-- pdf & excel btns -->
            <a href="#" onclick="saveDivAsPDF()"><button type="button">Save As PDF 📂</button></a>
            <a href="#" onclick="exportToExcel()"><button type="button">Save As XLS File 📂</button></a>
        <!-- filter by category -->
            <form action="/admin/saved-item/" method="get">
                <fieldset>
                    <legend>Filter By Category</legend>
                    <select name="search">
                        <option value="">-- All Category --</option>
                        <?php
                            $sql2 = "SELECT DISTINCT `category` FROM `Product`";
                            $stmt2 = mysqli_query($conn, $sql2);
                        /* store data */
                            while($row2 = mysqli_fetch_assoc($stmt2)) {
                                echo('<option value="' .$row2['category'] .'"' .((isset($_GET['search']) && $_GET['search'] == $row2['category']) ? ' selected>' : '>') .$row2['category'] .'</option>');
                            }
                        ?>
                    </select>
                    <button type="submit">Filter 🧼</button>
                </fieldset>
            </form>
        </div>
        <div class="search-div">
            <table id="myTable">
                <tr>
                    <th class="export">Product Name</th>
                    <th class="export">Description</th>
                </tr>
                <?php
                /* display load data */
                    if ($stmt == true) {
                        while ($row = mysqli_fetch_assoc($stmt)) {
                            echo "<tr>";
                            echo "<td class='export'>" .$row['productName'] ."</td>";
                            echo "<td class='export'>" .$row['description'] ."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </table>
        </div>
    <!-- PDF download -->
        <div class="quote-outline-border" id="pdf-content">
            <div class="quote-outline">
                <h1>INVOICE</h1>
                <div class="img-border"><img src="/image/logo.jpg" alt=""></div>
                <p>Dilla's PC, Indrani Mahal, Hanwella Road, Padukka, Sri Lanka</p>
                <h3>Issued to:</h3>
                <div class="header-part-1">
                    <p>Supply Dealers</p>
                </div>
                <div class="header-part-2">
                    <table>
                        <tr>
                            <td>Invoice No:</td>
                            <th>-</th>
                        </tr>
                        <tr>
                            <td>Date Issued:</td>
                            <th><?php echo(date("d/m/Y"));?></th>
                        </tr>
                    </table>
                </div>
                <div class="item-table-layout">
                    <table>
                        <thead>
                            <tr>
                                <th id="first-column">PRODUCT NAME</th>
                                <th id='first-column'>DESCRIPTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            /* display load data */
                                if ($stmt == true) {
                                    $stmt2 = mysqli_query($conn, $sql); # Create new query
                                    while ($row = mysqli_fetch_assoc($stmt2)) {
                                        echo "<tr>";
                                        echo "<td id='first-column'>" .$row['productName'] ."</td>";
                                        echo "<td id='first-column'>" .$row['description'] ."</td>";
                                        echo "</tr>";
                                    }
                                }
                            ?>
                            <tr id="total-table">
                                <td></td>
                                <td id="main-table-column">
                                    <table>
                                        <tr>
                                            <th style="font-weight: 900;">No. Of Items:</th>
                                            <td id='first-column' style="font-weight: 600;"><?php echo(mysqli_num_rows($stmt2)); ?></td>
                                        </tr>
                                        <tr>
                                            <th style="font-weight: normal;">Category of:</th>
                                            <td id='first-column'><?php echo((!isset($_GET['search']) || ((isset($_GET['search']) && $_GET['search'] == ''))) ? "All Items" : $_GET['search']); ?></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p style="margin: 30px auto; padding: 10px; font-size: 12px; color: #000000;">
                    <b>Dilla's PC, </b>Indrani Mahal, Hanwella Road, Padukka, Sri Lanka.&nbsp;
                    <b>E-mail: </b>anushkanawagamuwa@gmail.com
                </p>
            </div>
        </div>
        <div class="bottom-div">
        <!-- back to home -->
            <a href="/admin/dashboard/"><button type="button">Cancel</button></a>
        </div>
    </main>
    <footer>
        <div class="footer-about"><hr>
            <div class="about-title">
                <div class="footer-img"><img src="/image/logo.jpg" alt="dilla's pc"></div>
                <h1>DILLA'S PC</h1>
                <h6>Your trusted partner for all PC solution</h6>
            </div>
            <div class="footer-btn">
                <a href="/">Home</a> |
                <a href="/about-us/">About Us</a> |
                <a href="/contact-us/">Contact Us</a> |
                <a href="/admin/dashboard/">Admin</a>
            </div>
            <h4>&copy; 2025 Dilla's PC, Inc. All rights reserved.</h4>
        </div>
    </footer>
</body>
<script type="text/javascript">
// Export PDF
    function saveDivAsPDF() {
        document.getElementById("pdf-content").style.display = "block";

        const element = document.getElementById('pdf-content');

        html2canvas(element, { scale: 1.5, useCORS: true }).then(canvas => {
            // Convert to JPEG for better compression
            const imgData = canvas.toDataURL('image/jpeg', 0.6); // Lower quality (0.6)

            const imgWidth = 190; // Keep fixed width
            const imgHeight = (canvas.height * imgWidth) / canvas.width; // Maintain aspect ratio

            const pageWidth = 210; // A4 width in mm
            const pageHeight = imgHeight + 20; // Dynamic height with padding

            const pdf = new jspdf.jsPDF('p', 'mm', [pageWidth, pageHeight]);

            pdf.addImage(imgData, 'JPEG', 10, 10, imgWidth, imgHeight);
            pdf.save('In-Stock Items.pdf');
        });

        document.getElementById("pdf-content").style.display = "none";
    }
// Export Excel File
    function exportToExcel() {
        let table = document.getElementById("myTable");
        let rows = table.getElementsByTagName("tr");
        let data = [];

        for (let i = 0; i < rows.length; i++) { // Start from 0 to include headers
            let cells = rows[i].querySelectorAll(".export"); // Select only elements with class 'export'
            let rowData = [];

            for (let j = 0; j < cells.length; j++) {
                let input = cells[j].querySelector("input");

                if (input) {
                    rowData.push(input.value); // Get input value
                } else {
                    rowData.push(cells[j].innerText.trim()); // Get text from <th> or <td>
                }
            }

            if (rowData.length > 0) {
                data.push(rowData);
            }
        }

        let worksheet = XLSX.utils.aoa_to_sheet(data);
        let workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "In-stock");

        XLSX.writeFile(workbook, "In-stock-Products.xlsx");
    }
</script>
</html>