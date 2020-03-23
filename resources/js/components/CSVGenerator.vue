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
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(row, index) in data">
                                <td v-for="(dataColumn, columnName) in row">
                                    <input type="text" class="form-control" v-model="row[columnName]"/>
                                </td>
                                <td class="delete-cell">
                                    <button type="button" class="btn btn-secondary" @click="showRemoveRowConfirmation(index)">Delete</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <button type="button" class="btn btn-secondary" @click="addColumn(event)">Add Column</button>
                        <button type="button" class="btn btn-secondary" @click="addRow()">Add Row</button>
                    </div>

                    <div class="card-footer text-right">
                        <button class="btn btn-primary" type="button" @click="submit()">Export</button>
                    </div>
                </div>
            </div>
        </div>
        <b-modal id="delete-row-modal" @ok="removeRow(selectedIndex)">
            Are you sure you want to delete this row?
        </b-modal>
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
                ],
                selectedIndex: null
            }
        },

        methods: {
            addRow() {
                const newRow = {};
                this.columns.forEach(column => {
                    newRow[column.key] = '';
                });
                this.data.push(newRow);
            },

            removeRow(rowIndex) {
                // remove the given row
                this.data.splice(rowIndex, 1);
            },

            showRemoveRowConfirmation(rowIndex) {
                this.selectedIndex = rowIndex;
                this.$bvModal.show('delete-row-modal');
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
                        if (row.hasOwnProperty(oldKey)) {
                            row[column.key] = row[oldKey];
                            delete row[oldKey];
                        }
                    }
                )
            },

            async submit() {
                const response = await axios.patch('/api/csv-export', this.data);
                const type = response.headers['content-type'];
                const blob = new Blob([response.data], { 
                    type: type, encoding: 'UTF-8' 
                });
                const link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'CsvExport.csv';
                link.click();
            }
        },

        watch: {
        }
    }
</script>

<style scoped>

    .delete-cell {
        display: flex;
        justify-content: center;
    }

</style>
