<template>
    <div>
        <ActivityHistory
            ref="novaActivityHistory"
            :field="field"
            :resourceName="resourceName"
            :resourceId="resourceId"
        />
        <AddActivityForm
            :field="field"
            :resourceName="resourceName"
            :resourceId="resourceId"
            />
    </div>
</template>

<script>
    import ActivityHistory from "./ActivityHistory";
    import AddActivityForm from "./AddActivityForm";
    import { FormField, HandlesValidationErrors } from "laravel-nova";

    export default {
        mixins: [FormField, HandlesValidationErrors],

        props: ["resourceName", "field", "resourceId"],

        components: { ActivityHistory, AddActivityForm },

        data() {},
        created() {},

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
