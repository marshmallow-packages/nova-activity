<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <NovaActivityList
                :resourceName="resourceName"
                :field="field"
                :limit="field.limit_on_forms"
                :resourceId="resourceId"
                :commentFieldId="commentFieldId"
            />
        </template>
    </DefaultField>
</template>

<script>
    import NovaActivityList from "./NovaActivityList";
    import { FormField, HandlesValidationErrors } from "laravel-nova";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ["resourceName", "field", "resourceId"],

        components: { NovaActivityList },

        created() {
            let self = this;
            this.commentFieldId =
                "activity_id_" + Math.random().toString(36).substring(7);
            Nova.$on("novaActivitySetFormFieldValue", function (data) {
                self.setValue(data.key, data.value);
            });
        },

        data() {
            return {
                current_value: {},
                commentFieldId: null,
            };
        },

        methods: {
            setValue(key, value) {
                this.current_value[key] = value;
            },

            /*
             * Set the initial, internal value for the field.
             */
            setInitialValue() {
                this.value = this.field.value || "";
            },

            /**
             * Fill the given FormData object with the field's internal value.
             */
            fill(formData) {
                this.setValue(
                    "comment",
                    this.field.use_comments
                        ? document.getElementById(this.commentFieldId).value
                        : ""
                );
                this.setValue("mentions", this.field.mentions);

                let type_value = this.field.types.find(
                    (type) => type.value === this.current_value.type
                );

                if (type_value) {
                    this.setValue("type_label", type_value.text);
                }

                formData.append(
                    this.fieldAttribute,
                    JSON.stringify(this.current_value)
                );
            },
        },
    };
</script>
