<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | Set some default values. It is possible to add all defines that can be set
    | in dompdf_config.inc.php. You can also override the entire config file.
    |
    */
    'show_warnings' => false,   // Mostrar advertencias
    'orientation'   => 'portrait',
    'default_paper_size' => 'a4',
    
    /*
    |--------------------------------------------------------------------------
    | Default Font
    |--------------------------------------------------------------------------
    |
    | This option determines the default font family. This is useful when using
    | tailor made templates.
    |
    */
    'default_font' => 'sans-serif',
    
    /*
    |--------------------------------------------------------------------------
    | Dirs
    |--------------------------------------------------------------------------
    |
    */
    'font_dir' => storage_path('fonts/'), // This is the storage directory where fonts are stored
    'font_cache' => storage_path('fonts/'),
    'temp_dir' => sys_get_temp_dir(),     // Temp directory for caching images
    
    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | Dompdf options. Remember: When using custom fonts, they must be loaded.
    |
    */
    'options' => [
        /**
         * The location of the DOMPDF font directory
         *
         * The location of the directory where DOMPDF will store fonts and font metrics
         * Note: This directory must exist and be writable by the webserver process.
         * */
        "font_dir" => storage_path('fonts/'),
        
        /**
         * The location of the DOMPDF font cache directory
         *
         * This directory contains the cached font metrics for the fonts used by DOMPDF.
         * This directory can be the same as DOMPDF_FONT_DIR
         */
        "font_cache" => storage_path('fonts/'),
        
        /**
         * font_cache_max_files
         *
         * Maximum number of font metrics files cached in font_cache directory
         * When the number of files exceeds this limit, the least-recently-used
         * files are deleted from the cache
         */
        "font_cache_max_files" => 100,
        
        /**
         * Allow scripts to run in HTML?
         *
         * DOMPDF will process <script> tags if "isPhpEnabled" is true
         * (safe mode prevents scripts from reading system files, so this may be a security concern)
         */
        "isPhpEnabled" => true,
        
        /**
         * Enable inline PHP support
         *
         * If this setting is set to true then DOMPDF will automatically evaluate
         * inline PHP contained within <script type="text/php"> ... </script> tags.
         *
         * Enabling this for public websites is a security risk
         * It is recommended that you disable this feature.
         */
        "isPhpEnabled" => true,
        
        /**
         * Enable remote file access
         *
         * If this setting is set to true, DOMPDF will access remote sites for
         * images and CSS files as required.
         */
        "isRemoteEnabled" => true,
        
        /**
         * Enable HTML5 parsing
         *
         * Enables HTML5 parsing. This setting is for backwards compatibility.
         * HTML5 support now requires DOMDocument version >= 5.0
         */
        "isHtml5ParserEnabled" => true,
        
        /**
         * Enable CSS float
         *
         * Allows people to disabled CSS float support
         * Float support is a little buggy still, but generally usable
         */
        "isFontSubsettingEnabled" => true,
        
        /**
         * Use the more-than-experimental HTML5 Lib parser
         */
        "enable_html5_parser" => true,
        
        /**
         * This specific configuration ensures that all UTF-8 characters
         * are displayed correctly in the PDF
         */
        "defaultMediaType" => "screen",
        "defaultPaperSize" => "a4",
        "defaultFont" => "sans-serif",
        "dpi" => 96,
        "fontHeightRatio" => 1.1,
        
        /**
         * PDF/A compliance
         *
         * If true, DOMPDF will try to make the generated PDF
         * PDF/A compliant.
         */
        "enable_php" => false,
        "enable_javascript" => true,
        "enable_remote" => true,
        "pdfBackend" => "CPDF",
        "logOutputFile" => "",
        
        /**
         * A ratio applied to the fonts height to be more like browsers' line height
         */
        "line_height_ratio" => 1.25,
        
        /**
         * Increase page render accuracy by enabling table layout optimization
         */
        "enable_layout_passthrough" => true,
    ],
];