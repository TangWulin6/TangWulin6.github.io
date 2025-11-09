<!-- <div class="htmlExportLogs" style=" margin-top: 15px;margin-bottom: 15px;">
    <h4 class="progress-title p-t-15"><?php _e('Html export log', 'export-wp-page-to-static-html'); ?></h4>
    <span class="totalExported" style="margin-right: 10px"><?php _e('Exported:', 'export-wp-page-to-static-html'); ?> <span class="total_exported_files progress_">0</span></span>
    <span class="totalLogs"><?php _e('Fetched files:', 'export-wp-page-to-static-html'); ?> <span class="total_fetched_files total_">0</span></span>
    <div class="progress orange" style="margin-top: 20px">
        <div class="progress-bar" style="width:0%; background:#fe3b3b;">
            <div class="progress-value">0%</div>
        </div>
    </div>
    <div class="export_failed error" style="display: none;"><?php _e('Error, failed to export files!', 'export-wp-page-to-static-html'); ?> </div>

    <button class="flat-button pause" role="button" style="display: none;"><?php _e('Pause', 'export-wp-page-to-static-html'); ?></button>
    <button class="flat-button resume" role="button" style="display: none;"><?php _e('Resume', 'export-wp-page-to-static-html'); ?></button>
</div> -->


  <div class="htmlExportLogs" style="display: none;">
    <h4 class="progress-title">HTML export progress</h4>
    <div class="totals">
      <span class="totalExported">Exported: <span class="total_exported_files progress_">0</span></span>
      <span class="totalLogs">Fetched files: <span class="total_fetched_files total_">0</span></span>
    </div>

    <!-- Progress bar (keeps your classes) -->
    <div class="progress orange" id="progressRoot">
      <div class="progress-bar" id="bar" style="width:0%">
        <div class="progress-value" id="barValue">0%</div>
      </div>
      <div class="sr-only" id="srBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="Overall progress"></div>
    </div>

    <!-- Lower checkpoints only (as requested) -->
    <div class="checkpoints" id="checkpoints" aria-label="Export steps">
      <!-- HTML Files -->
      <div class="cp" id="cp-html">
        <div class="icon" aria-hidden="true">
          <!-- file-code icon -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <polyline points="10 13 8 15 10 17"/>
            <polyline points="14 13 16 15 14 17"/>
          </svg>
        </div>
        <div class="name">HTML Files</div>
        <div class="tick" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>
      </div>

      <!-- Assets -->
      <div class="cp" id="cp-assets">
        <div class="icon" aria-hidden="true">
          <!-- image icon -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <path d="M21 15l-5-5L5 21"/>
          </svg>
        </div>
        <div class="name">Assets</div>
        <div class="tick" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>
      </div>

      <!-- Zip -->
      <div class="cp" id="cp-zip">
        <div class="icon" aria-hidden="true">
          <!-- archive icon -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="4"/>
            <path d="M5 7v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7"/>
            <line x1="12" y1="11" x2="12" y2="17"/>
            <line x1="9"  y1="7" x2="9"  y2="3"/>
            <line x1="15" y1="7" x2="15" y2="3"/>
          </svg>
        </div>
        <div class="name">Zip</div>
        <div class="tick" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>
      </div>

      <!-- Upload (shown only if online upload = true) -->
      <div class="cp" id="cp-upload" hidden>
        <div class="icon" aria-hidden="true">
          <!-- upload icon -->
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
        </div>
        <div class="name">Upload</div>
        <div class="tick" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
        </div>
      </div>
    </div>

    <!-- ===== Logs ===== -->
    <section class="logs p-t-15" style="display:block;">
      <div class="header">
        <h4>Export log</h4>
      </div>
      <div class="logs-scroll">
        <div class="logs_list" id="logsList">
        </div>
      </div>
    </section>
  </div>
