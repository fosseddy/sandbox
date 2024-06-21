import { AuthorModel } from "./model.mjs";
import { isFormValid } from "#src/validator.mjs";

export async function indexView(req, res) {
    const authors = await AuthorModel.findAll();
    res.render("author/index", { authors });
}

export function showView(req, res) {
    res.render("author/show", { author: req.author });
}

export function createView(req, res) {
    res.render("author/create", {
        form: { name: "", date_of_birth: "", date_of_death: "", errors: {} }
    });
}

export function updateView(req, res) {
    const fields = { ...req.author };
    delete fields.id;

    res.render("author/update", {
        form: { ...fields, errors: {} }
    });
}

export function removeView(req, res) {
    res.render("author/delete", { author: req.author });
}

export async function create(req, res) {
    const { form } = req;

    if (!isFormValid(form)) {
        return res.render("author/create", { form });
    }

    delete form.errors;
    const id = await AuthorModel.insert(form);

    res.redirect(`/catalog/author/${id}`);
}

export async function update(req, res) {
    const { form } = req;

    if (!isFormValid(form)) {
        return res.render("author/update", { form });
    }

    delete form.errors;
    await AuthorModel.update(req.params.id, form);

    res.redirect(`/catalog/author/${req.params.id}`);
}

export async function remove(req, res) {
    const { id } = req.body;

    if (id != req.params.id) {
        throw new Error(`Author with id ${id} does not exist`);
    }

    await AuthorModel.remove(id);

    res.redirect("/catalog/author");
}
