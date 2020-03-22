<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">Table to CSV Generator</div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th v-for="column in columns">
                                    <input type="text"
                                           class="form-control"
                                           :value="column.key"
                                           :ref="('title_' + column.key)"
                                           @input="updateColumnKey(column, $event)"
                                    />
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="row in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" @click="addColumn(event)">Add Column</button>
                        <button type="button" class="btn btn-secondary">Add Row</button>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="button" @click="submit()">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        name: "CSVGenerator",

        data() {
            return {
                data: [
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },
                    {
                        first_name: 'John',
                        last_name: 'Doe',
                        emailAddress: 'john.doe@example.com'
                    },

                ],
                columns: [
                    {key: 'first_name'},
                    {key: 'last_name'},
                    {key: 'emailAddress'},

                ]
            }
        },

        methods: {
            addRow() {
                // Add new row to data with column keys
            },

            removeRow(row_index) {
                // remove the given row
            },

            addColumn(event) {
                const newKey = 'newColumn_' + this.columns.length;
                this.columns.push({key: newKey});
                this.data.forEach(row => {
                  row[newKey] = '';
                })
                this.$nextTick(() => {
                    const titleInput = this.$refs['title_' + newKey][0];
                    titleInput.focus();
                    titleInput.select();
                })
            },

            updateColumnKey(column, event) {
                let oldKey = column.key;

                let columnKeyExists = !!this.columns.find(column => column.key === event.target.value);

                column.key = event.target.value;

                if (columnKeyExists) {
                    column.key = event.target.value.substring(0, event.target.value.length - 1);
                    return;
                }

                this.data.forEach(
                    (row) => {
                        if (row[oldKey]) {
                            row[column.key] = row[oldKey];
                            delete row[oldKey];
                        }
                    }
                )
            },

            submit() {
                return axios.patch('/api/csv-export', this.data);
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

</style>
