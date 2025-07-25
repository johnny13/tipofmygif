#!/bin/bash

# Generate OpenAPI documentation
echo "Generating OpenAPI documentation..."

# Create the output directory if it doesn't exist
mkdir -p storage/api-docs

# Generate the API documentation
./vendor/bin/openapi --output storage/api-docs/api-docs.json app/Http/Controllers app/Http/Annotations app/Models

if [ $? -eq 0 ]; then
    echo "✅ OpenAPI documentation generated successfully!"
    echo "📄 Documentation saved to: storage/api-docs/api-docs.json"
    echo "📊 You can view it at: http://localhost:8000/api/documentation (if using L5-Swagger)"
else
    echo "❌ Error generating OpenAPI documentation"
    exit 1
fi 