function isFormValid(form) {
    return Object.keys(form.errors).length === 0;
}

export { isFormValid };
