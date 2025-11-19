#!/bin/bash

# Production Build Script for TNT Branded Backend Plugin
# This script prepares the plugin for deployment to live websites

set -e  # Exit on error

echo "🚀 Building TNT Branded Backend Plugin for production..."

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if node_modules exists
if [ ! -d "node_modules" ]; then
    echo -e "${YELLOW}📦 Installing dependencies...${NC}"
    npm install
fi

# Compile SCSS to CSS (minified)
echo -e "${GREEN}🎨 Compiling SCSS files...${NC}"
npm run build

# Check if CSS files were generated
if [ ! -f "assets/css/login.css" ]; then
    echo -e "${YELLOW}⚠️  Warning: login.css not found. Make sure SCSS compilation succeeded.${NC}"
fi

if [ ! -f "assets/css/cms.css" ]; then
    echo -e "${YELLOW}⚠️  Warning: cms.css not found. Make sure SCSS compilation succeeded.${NC}"
fi

# Remove source maps in production (optional - uncomment if needed)
# echo -e "${GREEN}🗑️  Removing source maps...${NC}"
# find assets/css -name "*.map" -type f -delete

# Remove node_modules for deployment (optional - uncomment if you don't want to include them)
# echo -e "${GREEN}🗑️  Removing node_modules...${NC}"
# rm -rf node_modules

echo -e "${GREEN}✅ Build complete! Plugin is ready for deployment.${NC}"
echo ""
echo "📋 Pre-deployment checklist:"
echo "   ✓ SCSS compiled to CSS"
echo "   ✓ All files in place"
echo ""
echo "📦 Next steps:"
echo "   1. Test the plugin on a staging site"
echo "   2. Verify all functionality works"
echo "   3. Zip the plugin folder (excluding node_modules and .git)"
echo "   4. Upload to production site"

