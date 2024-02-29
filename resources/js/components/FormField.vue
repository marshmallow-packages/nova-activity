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

        data() {
            return {
                current_value: {},
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
                        ? document.getElementById("comment").value
                        : ""
                );
                this.setValue("mentions", this.field.mentions);

                let type_value = this.field.types.find(
                    (type) => type.value === this.current_value.type
                );

                this.setValue("type_label", type_value.text);

                formData.append(
                    this.fieldAttribute,
                    JSON.stringify(this.current_value)
                );
            },
        },
    };
</script>
