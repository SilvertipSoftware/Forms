<?php

namespace SilvertipSoftware\Forms\Tags;

class PasswordField extends TextField {

    protected function addValueFromFlash(&$options) {
        // no-op. we ignore flash for passwords
    }

    protected function value() {
        return '';
    }
}
