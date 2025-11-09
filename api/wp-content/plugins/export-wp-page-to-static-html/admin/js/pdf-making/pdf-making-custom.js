let pdfAlreadyTriggered = false;

window.addEventListener('load', function () {
    if (typeof jQuery !== 'undefined') {
        let ajaxPending = false;

        jQuery(document).ajaxStart(function () {
            ajaxPending = true;
        });

        jQuery(document).ajaxStop(function () {
            ajaxPending = false;

            if (!pdfAlreadyTriggered) {
                pdfAlreadyTriggered = true;

                setTimeout(() => {
                    triggerPDFDownload();
                }, 200);
            }
        });

        // Fallback in case no AJAX is triggered
        setTimeout(() => {
            if (!ajaxPending && !pdfAlreadyTriggered) {
                pdfAlreadyTriggered = true;
                triggerPDFDownload();
            }
        }, 1000);
    } else {
        triggerPDFDownload();
    }
});


function triggerPDFDownload() {
    const element = document.getElementById("page");
    if (!element) return;

    html2pdf().set({
        margin: 10,
        filename: EWPPTSH_WP_PageData.current_page + '.pdf',
        image: { type: 'jpeg', quality: 0.99 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    })
    .from(element)
    .save()
    .then(() => {
        
        const modal = document.getElementById('pdf-download-modal');
        if (modal) modal.style.display = 'none';

        updatePdfDownloadCount();
    });
}


function updatePdfDownloadCount() {

    jQuery.ajax({
        url: EWPPTSH_WP_Ajax.ajax_url,
        type: 'POST',
        data: {
            action: 'ewpptsh_increment_pdf_count',
            rc_nonce: EWPPTSH_WP_Ajax.nonce,
        },
        success: function(response) {
            console.log('AJAX Response:', response);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });
    
}

