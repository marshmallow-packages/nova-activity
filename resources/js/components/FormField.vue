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

        methods: {
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
                formData.append(this.fieldAttribute, this.value || "");
            },
        },
    };
</script>
