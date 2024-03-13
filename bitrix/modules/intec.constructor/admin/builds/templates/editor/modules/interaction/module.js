(function () {
    return {
        'created': function () {
            window.addEventListener('keydown', this.keyPressed);
        },
        'destroyed': function () {
            window.removeEventListener('keydown', this.keyPresed);
        },
        'methods': {
            'keyPressed': function (event) {
                if (!this.hasSelection)
                    return;

                if (event.target && (
                    event.target.tagName === 'INPUT' ||
                    event.target.tagName === 'TEXTAREA'
                )) return;

                switch (event.keyCode) {
                    /** Escape */
                    case 27: {
                        this.removeSelection();
                        break;
                    }
                    /** Delete */
                    case 46: {
                        this.removeSelectedContainer();
                        break;
                    }
                    /** Arrow up */
                    case 38: {
                        if (event.ctrlKey) {
                            this.selectPreviousContainer();
                        } else if (event.shiftKey) {
                            this.orderUpSelectedContainer();
                        } else if (event.altKey) {
                            this.selectPreviousLevelContainer();
                        }

                        break;
                    }
                    /** Arrow down */
                    case 40: {
                        if (event.ctrlKey) {
                            this.selectNextContainer();
                        } else if (event.shiftKey) {
                            this.orderDownSelectedContainer();
                        } else if (event.altKey) {
                            this.selectNextLevelContainer();
                        }

                        break;
                    }
                    /** Button "x" */
                    case 88: {
                        if (event.ctrlKey)
                            this.storeSelectedContainerInBuffer(true);

                        break;
                    }
                    /** Button "c" */
                    case 67: {
                        if (event.ctrlKey)
                            this.storeSelectedContainerInBuffer(false);

                        break;
                    }
                    /** Button "v" */
                    case 86: {
                        if (event.ctrlKey)
                            this.pasteContainerToSelectionFromBuffer();

                        break;
                    }
                }
            }
        }
    }
})();
