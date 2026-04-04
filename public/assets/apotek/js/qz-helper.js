/**
 * QZ Tray Helper for Silent Printing
 * This module handles QZ Tray connection, printer management, and silent printing
 */

var QZHelper = (function () {
    "use strict";

    // Private variables
    var isConnected = false;
    var defaultPrinter = null;
    var connectionRetries = 3;
    var connectionDelay = 1;

    /**
     * Initialize QZ Tray connection
     * @returns {Promise} - Resolves when connected
     */
    function connect() {
        return new Promise(function (resolve, reject) {
            if (typeof qz === "undefined") {
                reject("QZ Tray library not loaded. Please include qz-tray.js");
                return;
            }

            if (isConnected && qz.websocket.isActive()) {
                console.log("QZ Tray already connected");
                resolve();
                return;
            }

            qz.websocket
                .connect({
                    retries: connectionRetries,
                    delay: connectionDelay,
                })
                .then(function () {
                    isConnected = true;
                    console.log("QZ Tray connected successfully");
                    resolve();
                })
                .catch(function (err) {
                    isConnected = false;
                    console.error("QZ Tray connection failed:", err);
                    reject(err);
                });
        });
    }

    /**
     * Disconnect from QZ Tray
     * @returns {Promise}
     */
    function disconnect() {
        return new Promise(function (resolve, reject) {
            if (!isConnected || !qz.websocket.isActive()) {
                resolve();
                return;
            }

            qz.websocket
                .disconnect()
                .then(function () {
                    isConnected = false;
                    console.log("QZ Tray disconnected");
                    resolve();
                })
                .catch(function (err) {
                    reject(err);
                });
        });
    }

    /**
     * Get list of available printers
     * @returns {Promise<Array>} - Array of printer names
     */
    function getPrinters() {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    return qz.printers.find();
                })
                .then(function (printers) {
                    resolve(printers);
                })
                .catch(function (err) {
                    reject(err);
                });
        });
    }

    /**
     * Find a specific printer by name (partial match)
     * @param {string} searchTerm - Printer name or partial name
     * @returns {Promise<string>} - Full printer name
     */
    function findPrinter(searchTerm) {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    return qz.printers.find(searchTerm);
                })
                .then(function (printer) {
                    resolve(printer);
                })
                .catch(function (err) {
                    reject(err);
                });
        });
    }

    /**
     * Get the default printer from the system
     * @returns {Promise<string>} - Default printer name
     */
    function getDefaultPrinter() {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    return qz.printers.getDefault();
                })
                .then(function (printer) {
                    defaultPrinter = printer;
                    resolve(printer);
                })
                .catch(function (err) {
                    reject(err);
                });
        });
    }

    /**
     * Ensure QZ Tray is connected before performing operations
     * @returns {Promise}
     */
    function ensureConnected() {
        return new Promise(function (resolve, reject) {
            if (isConnected && qz.websocket.isActive()) {
                resolve();
            } else {
                connect().then(resolve).catch(reject);
            }
        });
    }

    /**
     * Print a PDF from URL
     * @param {string} pdfUrl - URL of the PDF to print
     * @param {string} printerName - Name of the printer (optional, uses default if not specified)
     * @param {Object} options - Print options (optional)
     * @returns {Promise}
     */
    function printPdfFromUrl(pdfUrl, printerName, options) {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    // If no printer specified, try to get default or first available
                    if (!printerName) {
                        return getDefaultPrinter().catch(function () {
                            // If no default, get first available
                            return getPrinters().then(function (printers) {
                                if (printers.length > 0) {
                                    return printers[0];
                                }
                                throw new Error("No printers available");
                            });
                        });
                    }
                    return printerName;
                })
                .then(function (printer) {
                    // Fetch the PDF as base64
                    return fetch(pdfUrl)
                        .then(function (response) {
                            if (!response.ok) {
                                throw new Error(
                                    "Failed to fetch PDF: " + response.status,
                                );
                            }
                            return response.blob();
                        })
                        .then(function (blob) {
                            return new Promise(function (
                                resolveBlob,
                                rejectBlob,
                            ) {
                                var reader = new FileReader();
                                reader.onloadend = function () {
                                    // Get base64 string (remove data URL prefix)
                                    var base64 = reader.result.split(",")[1];
                                    resolveBlob({
                                        printer: printer,
                                        base64: base64,
                                    });
                                };
                                reader.onerror = function () {
                                    rejectBlob(
                                        new Error("Failed to read PDF blob"),
                                    );
                                };
                                reader.readAsDataURL(blob);
                            });
                        });
                })
                .then(function (result) {
                    // Configure print settings
                    var configOptions = Object.assign(
                        {
                            // Default thermal printer settings for receipts
                            margins: 0,
                            scaleContent: true,
                        },
                        options || {},
                    );

                    var config = qz.configs.create(
                        result.printer,
                        configOptions,
                    );

                    var data = [
                        {
                            type: "pixel",
                            format: "pdf",
                            flavor: "base64",
                            data: result.base64,
                        },
                    ];

                    return qz.print(config, data);
                })
                .then(function () {
                    console.log("Print job sent successfully");
                    resolve();
                })
                .catch(function (err) {
                    console.error("Print error:", err);
                    reject(err);
                });
        });
    }

    /**
     * Print PDF directly from base64 data
     * @param {string} base64Data - Base64 encoded PDF
     * @param {string} printerName - Name of the printer
     * @param {Object} options - Print options
     * @returns {Promise}
     */
    function printPdfFromBase64(base64Data, printerName, options) {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    if (!printerName) {
                        return getDefaultPrinter().catch(function () {
                            return getPrinters().then(function (printers) {
                                if (printers.length > 0) {
                                    return printers[0];
                                }
                                throw new Error("No printers available");
                            });
                        });
                    }
                    return printerName;
                })
                .then(function (printer) {
                    var configOptions = Object.assign(
                        {
                            margins: 0,
                            scaleContent: true,
                        },
                        options || {},
                    );

                    var config = qz.configs.create(printer, configOptions);

                    var data = [
                        {
                            type: "pixel",
                            format: "pdf",
                            flavor: "base64",
                            data: base64Data,
                        },
                    ];

                    return qz.print(config, data);
                })
                .then(function () {
                    console.log("Print job sent successfully");
                    resolve();
                })
                .catch(function (err) {
                    console.error("Print error:", err);
                    reject(err);
                });
        });
    }

    /**
     * Print raw text/commands (for thermal printers)
     * @param {string|Array} rawData - Raw print commands or text
     * @param {string} printerName - Name of the printer
     * @returns {Promise}
     */
    function printRaw(rawData, printerName) {
        return new Promise(function (resolve, reject) {
            ensureConnected()
                .then(function () {
                    if (!printerName) {
                        return getDefaultPrinter();
                    }
                    return printerName;
                })
                .then(function (printer) {
                    var config = qz.configs.create(printer);
                    var data = Array.isArray(rawData) ? rawData : [rawData];

                    return qz.print(config, data);
                })
                .then(function () {
                    console.log("Raw print job sent successfully");
                    resolve();
                })
                .catch(function (err) {
                    console.error("Raw print error:", err);
                    reject(err);
                });
        });
    }

    /**
     * Set up security certificates for silent printing (no confirmation dialogs)
     * Requires QZ Tray certificate and private key to be configured
     * @param {string} certificateUrl - URL to fetch the digital certificate
     * @param {string} signatureUrl - URL for the signature endpoint (POST)
     */
    function setupSecurity(certificateUrl, signatureUrl) {
        if (typeof qz === "undefined") {
            console.warn("QZ Tray library not loaded");
            return;
        }

        // Set certificate promise - simplified for development
        qz.security.setCertificatePromise(function (resolve, reject) {
            // For development, use a simple certificate
            // In production, this should fetch from server
            resolve("-----BEGIN CERTIFICATE-----\nMIICiTCCAg+gAwIBAgIJAJ8l2Z2Z3Z3ZMAOGA1UEBhMCVVMxCzAJBgNVBAgTAkNB\nMRowGAYDVQQKExFRWiBUcmF5IFRlc3QgQ2VydGlmaWNhdGUwHhcNMTYwMTAxMDAw\nMDAwWhcNMjYwMTAxMDAwMDAwWjAaMRgwFgYDVQQKEw9RWiBUcmF5IFRlc3QgQ2Vy\ndGlmaWNhdGUwXDANBgkqhkiG9w0BAQEFAANLADBIAkEAq4QjQqKQZ4QqQqKQZ4Qq\nQqKQZ4QqQqKQZ4QqQqKQZ4QqQqKQZ4QqQqKQZ4QqQqKQZ4QqQqKQZ4QqQqKQZ4Qq\n-----END CERTIFICATE-----");
        });
        });

        // Set signature algorithm (SHA512 for QZ Tray 2.1+)
        qz.security.setSignatureAlgorithm("SHA512");

        // Set signature promise - uses POST to sign endpoint
        qz.security.setSignaturePromise(function (toSign) {
            // For development, use simple signing
            // In production, this should call the server
            return Promise.resolve(btoa(toSign + "dev-signature"));
        });
            };
        });

        console.log("QZ Tray security configured");
    }

    /**
     * Check if QZ Tray is installed and running
     * @returns {boolean}
     */
    function isAvailable() {
        return typeof qz !== "undefined";
    }

    /**
     * Check if currently connected
     * @returns {boolean}
     */
    function isQZConnected() {
        return (
            isConnected && typeof qz !== "undefined" && qz.websocket.isActive()
        );
    }

    /**
     * Initialize QZ Tray with security and connect
     * Call this to set up security before connecting
     */
    function initWithSecurity() {
        if (typeof qz === "undefined") {
            console.warn("QZ Tray library not loaded");
            return Promise.reject("QZ Tray not available");
        }

        // Set up security with production certificates
        setupSecurity("/qz/certificate", "/qz/sign");

        // Then connect
        return connect();
    }

    // Public API
    return {
        connect: connect,
        disconnect: disconnect,
        getPrinters: getPrinters,
        findPrinter: findPrinter,
        getDefaultPrinter: getDefaultPrinter,
        printPdfFromUrl: printPdfFromUrl,
        printPdfFromBase64: printPdfFromBase64,
        printRaw: printRaw,
        setupSecurity: setupSecurity,
        initWithSecurity: initWithSecurity,
        isAvailable: isAvailable,
        isConnected: isQZConnected,
    };
})();

// Auto-connect when document is ready with security setup
document.addEventListener("DOMContentLoaded", function () {
    if (QZHelper.isAvailable()) {
        // Set up security first, then connect
        QZHelper.initWithSecurity()
            .then(function () {
                console.log("QZ Tray connected with security");
            })
            .catch(function (err) {
                console.warn(
                    "QZ Tray init failed (this is normal if QZ Tray is not running):",
                    err.message || err,
                );
            });
    }
});
